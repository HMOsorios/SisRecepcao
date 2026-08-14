<?php

namespace App\Services\Bi;

use App\Services\Novosga\NovosgaClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Indicadores de BI (Seção 2.5/8.1): agregados de atendimento consumidos do
 * NovoSGA via API de leitura. Em modo de simulação, devolve dados de
 * demonstração quando o NovoSGA está indisponível.
 */
class BiService
{
    public const STATUS_ATIVOS = ['SENHA_EMITIDA', 'CHAMADO', 'EM_ATENDIMENTO'];

    /**
     * Memoização por requisição: resumo()/porSetor()/porStatus() chamam
     * atendimentos() com os mesmos parâmetros — sem isso, um único carregamento
     * do dashboard da Diretoria dispara 3-4 chamadas HTTP redundantes ao NovoSGA.
     *
     * @var array<string,array<int,array<string,mixed>>>
     */
    protected array $cacheAtendimentos = [];

    /** @var array<int,array<string,mixed>>|null */
    protected ?array $cacheFila = null;

    public function __construct(
        protected readonly NovosgaClient $client,
        protected readonly bool $simulation,
        protected readonly int $unidadeId,
        protected readonly int $amostra,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            client: app(NovosgaClient::class),
            simulation: (bool) config('novosga.simulation', false),
            unidadeId: (int) config('sms.unidade_id', 1),
            amostra: (int) config('bi.amostra', 500),
        );
    }

    /**
     * Resumo geral: volumes e tempos médios.
     *
     * @return array<string,mixed>
     */
    public function resumo(): array
    {
        $atendimentos = $this->atendimentos();
        $fila = $this->fila();

        $hoje = array_values(array_filter($atendimentos, fn (array $a) => $this->data($a, 'dataChegada')?->isToday() ?? false));
        $finalizados = array_values(array_filter($atendimentos, fn (array $a) => $a['status'] === 'FINALIZADO'));

        return [
            'total_dia' => count($hoje),
            'em_fila' => count($fila),
            'em_atendimento' => count(array_filter($atendimentos, fn (array $a) => in_array($a['status'] ?? null, ['EM_ATENDIMENTO', 'CHAMADO'], true))),
            'tempo_medio_espera_s' => $this->media(fn (array $a) => $this->segundos($a, 'tempoEspera'), $finalizados),
            'tempo_medio_atendimento_s' => $this->media(fn (array $a) => $this->segundos($a, 'tempoAtendimento'), $finalizados),
            'nao_compareceu' => count(array_filter($atendimentos, fn (array $a) => $a['status'] === 'NAO_COMPARECEU')),
            'atualizado_em' => now()->toIso8601String(),
        ];
    }

    /**
     * Volume por serviço/setor.
     *
     * @return array<int,array<string,mixed>>
     */
    public function porSetor(): array
    {
        $atendimentos = $this->atendimentos();
        $agrupados = [];

        foreach ($atendimentos as $a) {
            $nome = (string) ($a['servico']['nome'] ?? 'Não informado');
            $agrupados[$nome] = ($agrupados[$nome] ?? 0) + 1;
        }

        arsort($agrupados);

        return array_map(fn (string $nome, int $total) => [
            'setor' => $nome,
            'total' => $total,
        ], array_keys($agrupados), $agrupados);
    }

    /**
     * Distribuição por status.
     *
     * @return array<int,array<string,mixed>>
     */
    public function porStatus(): array
    {
        $status = [
            'SENHA_EMITIDA' => 0,
            'CHAMADO' => 0,
            'EM_ATENDIMENTO' => 0,
            'FINALIZADO' => 0,
            'NAO_COMPARECEU' => 0,
            'CANCELADO' => 0,
        ];

        foreach ($this->atendimentos() as $a) {
            $s = (string) ($a['status'] ?? '');
            if (array_key_exists($s, $status)) {
                $status[$s]++;
            }
        }

        return array_map(fn (string $chave, int $total) => [
            'status' => $chave,
            'rotulo' => str_replace('_', ' ', $chave),
            'total' => $total,
        ], array_keys($status), $status);
    }

    /**
     * Busca textual em atendimentos (Seção 8.1 "busca em tempo real").
     *
     * @return array<int,array<string,mixed>>
     */
    public function buscar(string $termo, int $max = 30): array
    {
        $termo = trim($termo);
        if ($termo === '') {
            return [];
        }

        $atendimentos = $this->atendimentos(['limit' => 300]);
        $termoLower = mb_strtolower($termo);

        $resultado = array_values(array_filter($atendimentos, function (array $a) use ($termoLower): bool {
            $alvo = implode(' ', [
                (string) ($a['id'] ?? ''),
                (string) ($a['cliente']['nome'] ?? ''),
                (string) ($a['cliente']['documento'] ?? ''),
                (string) ($a['servico']['nome'] ?? ''),
                (string) ($a['senha']['sigla'] ?? ''),
                (string) ($a['senha']['numero'] ?? ''),
                (string) ($a['local']['nome'] ?? ''),
            ]);

            return mb_strpos(mb_strtolower($alvo), $termoLower) !== false;
        }));

        return array_slice($resultado, 0, $max);
    }

    /**
     * Atendimentos (com fallback de demonstração quando em simulação).
     *
     * @return array<int,array<string,mixed>>
     */
    public function atendimentos(array $params = []): array
    {
        $chave = json_encode($params) ?: '[]';

        if (isset($this->cacheAtendimentos[$chave])) {
            return $this->cacheAtendimentos[$chave];
        }

        try {
            $dados = $this->client->atendimentos(array_merge([
                'sort' => 'id',
                'order' => 'desc',
                'limit' => $this->amostra,
                'offset' => 0,
            ], $params));

            $resultado = array_values(array_filter($dados, 'is_array'));
        } catch (\Throwable $e) {
            Log::warning("[admin-bi] atendimentos indisponível: {$e->getMessage()}");

            $resultado = $this->simulation ? $this->demonstracao() : [];
        }

        return $this->cacheAtendimentos[$chave] = $resultado;
    }

    protected function fila(): array
    {
        if ($this->cacheFila !== null) {
            return $this->cacheFila;
        }

        try {
            $fila = $this->client->fila($this->unidadeId);

            return $this->cacheFila = array_values(array_filter($fila, 'is_array'));
        } catch (\Throwable) {
            return $this->cacheFila = [];
        }
    }

    protected function data(array $a, string $campo): ?Carbon
    {
        $valor = $a[$campo] ?? null;
        if ($valor === null || $valor === '') {
            return null;
        }

        try {
            return Carbon::parse((string) $valor);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Converte tempo (int segundos, string ISO8601 ou DateInterval serializado)
     * para segundos.
     */
    protected function segundos(array $a, string $campo): ?int
    {
        $valor = $a[$campo] ?? null;
        if ($valor === null || $valor === '') {
            return null;
        }

        if (is_int($valor) || is_float($valor)) {
            return (int) round((float) $valor);
        }

        if (is_array($valor)) {
            $s = (int) ($valor['s'] ?? 0);
            $i = (int) ($valor['i'] ?? 0);
            $h = (int) ($valor['h'] ?? 0);
            $d = (int) ($valor['d'] ?? 0);

            return $s + $i * 60 + $h * 3600 + $d * 86400;
        }

        try {
            return (int) Carbon::parse((string) $valor)->diffInSeconds(Carbon::parse('epoch'));
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param  callable(array):?int  $extrai
     */
    protected function media(callable $extrai, array $itens): ?int
    {
        $valores = array_values(array_filter(array_map($extrai, $itens), fn (?int $v) => $v !== null));

        if ($valores === []) {
            return null;
        }

        return (int) round(array_sum($valores) / count($valores));
    }

    /**
     * Dados de demonstração (desenvolvimento/demonstração).
     *
     * @return array<int,array<string,mixed>>
     */
    protected function demonstracao(): array
    {
        $hoje = now();

        return [
            ['id' => 1, 'status' => 'FINALIZADO', 'servico' => ['nome' => 'Atendimento Geral'], 'dataChegada' => $hoje->copy()->subMinutes(40)->toIso8601String(), 'tempoEspera' => 900, 'tempoAtendimento' => 600],
            ['id' => 2, 'status' => 'FINALIZADO', 'servico' => ['nome' => 'Gabinete'], 'dataChegada' => $hoje->copy()->subMinutes(30)->toIso8601String(), 'tempoEspera' => 450, 'tempoAtendimento' => 1200],
            ['id' => 3, 'status' => 'FINALIZADO', 'servico' => ['nome' => 'Ouvidoria'], 'dataChegada' => $hoje->copy()->subMinutes(20)->toIso8601String(), 'tempoEspera' => 300, 'tempoAtendimento' => 900],
            ['id' => 4, 'status' => 'FINALIZADO', 'servico' => ['nome' => 'Transporte (TFD)'], 'dataChegada' => $hoje->copy()->subMinutes(15)->toIso8601String(), 'tempoEspera' => 600, 'tempoAtendimento' => 750],
            ['id' => 5, 'status' => 'EM_ATENDIMENTO', 'servico' => ['nome' => 'Atendimento Geral'], 'dataChegada' => $hoje->copy()->subMinutes(10)->toIso8601String()],
            ['id' => 6, 'status' => 'SENHA_EMITIDA', 'servico' => ['nome' => 'RH / Folha'], 'dataChegada' => $hoje->copy()->subMinutes(5)->toIso8601String()],
        ];
    }
}
