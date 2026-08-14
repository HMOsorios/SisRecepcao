<?php

namespace App\Services\Fila;

use App\Models\SenhaOutbox;
use App\Services\Novosga\NovosgaClient;
use App\Services\Novosga\NovosgaIndisponivelException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

/**
 * Orquestra a emissão de senhas no totem (Módulo 2.1).
 *
 * Padrão outbox local + reconciliação (Seção 9.1):
 *  - NovoSGA disponível  → POST /api/distribui (fonte única de numeração).
 *  - NovoSGA indisponível → senha provisória local (OFF-XXXX) gravada no outbox;
 *    um job assíncrono reenvia quando a conectividade voltar.
 */
class EmissaoSenhaService
{
    public function __construct(
        protected readonly NovosgaClient $client,
        protected readonly int $unidadeId,
        protected readonly bool $simulation,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            client: app(NovosgaClient::class),
            unidadeId: (int) config('sms.unidade_id', 1),
            simulation: (bool) config('novosga.simulation', false),
        );
    }

    /**
     * @param  array<string,mixed>|null  $cliente
     * @param  array<string,mixed>|null  $metadata
     * @return array{provisional: bool, atendimento?: array<string,mixed>, outbox?: SenhaOutbox}
     */
    public function emitir(int $servico, int $prioridade, ?array $cliente = null, ?array $metadata = null): array
    {
        if (! $this->simulation) {
            try {
                $atendimento = $this->client->distribuiSenha(
                    $this->unidadeId,
                    $servico,
                    $prioridade,
                    $cliente,
                    $metadata,
                );

                return ['provisional' => false, 'atendimento' => $atendimento];
            } catch (NovosgaIndisponivelException $e) {
                Log::warning('[totem] NovoSGA indisponível, emitindo senha provisória: '.$e->getMessage());
            }
        }

        return $this->emitirProvisoria($servico, $prioridade, $cliente, $metadata);
    }

    /**
     * Emite senha provisória local e grava no outbox para reconciliação.
     *
     * @param  array<string,mixed>|null  $cliente
     * @param  array<string,mixed>|null  $metadata
     * @return array{provisional: true, outbox: SenhaOutbox}
     */
    protected function emitirProvisoria(int $servico, int $prioridade, ?array $cliente, ?array $metadata): array
    {
        $prefixo = (string) config('novosga.provisional_prefix', 'OFF');

        // contadorProvisorias() + INSERT não é atômico: duas emissões
        // concorrentes podem calcular o mesmo próximo número. A coluna
        // senha_provisoria tem unique() no banco, então a segunda tentativa
        // colide — em vez de deixar o QueryException estourar pro visitante,
        // retentamos com o próximo número (mesmo padrão de CrachaService::gerarCodigo).
        $tentativas = 0;
        $ultimoErro = null;

        while ($tentativas < 5) {
            $numero = (string) str_pad((string) ($this->contadorProvisorias() + 1 + $tentativas), 4, '0', STR_PAD_LEFT);

            try {
                $outbox = SenhaOutbox::create([
                    'senha_provisoria' => "{$prefixo}-{$numero}",
                    'unidade_id' => $this->unidadeId,
                    'servico_id' => $servico,
                    'prioridade_id' => $prioridade,
                    'cliente' => $cliente,
                    'metadata' => $metadata,
                    'status' => SenhaOutbox::STATUS_PENDENTE,
                    'data_chegada' => now(),
                ]);

                return ['provisional' => true, 'outbox' => $outbox];
            } catch (QueryException $e) {
                $ultimoErro = $e;
                $tentativas++;
            }
        }

        throw $ultimoErro;
    }

    /**
     * Reconciliação: reenvia as senhas provisórias pendentes ao NovoSGA,
     * preservando a ordem real de chegada.
     */
    public function reenviarPendentes(int $max = 50): int
    {
        $enviadas = 0;
        $maxTentativas = (int) config('novosga.outbox_max_tentativas', 8);

        SenhaOutbox::query()
            ->whereIn('status', [SenhaOutbox::STATUS_PENDENTE, SenhaOutbox::STATUS_FALHOU])
            ->orderBy('data_chegada')
            ->limit($max)
            ->get()
            ->each(function (SenhaOutbox $outbox) use (&$enviadas, $maxTentativas): void {
                if ($this->simulation) {
                    return;
                }

                // Reivindica a linha atomicamente (ENVIANDO) antes de chamar o
                // NovoSGA: se este processo morrer entre o sucesso remoto e o
                // update final, a linha fica "presa" em ENVIANDO — não é
                // reenviada automaticamente e não duplica a senha real
                // (Seção 9.1). Também protege contra duas execuções concorrentes
                // do job pegarem a mesma linha.
                $reivindicada = SenhaOutbox::query()
                    ->whereKey($outbox->id)
                    ->whereIn('status', [SenhaOutbox::STATUS_PENDENTE, SenhaOutbox::STATUS_FALHOU])
                    ->update(['status' => SenhaOutbox::STATUS_ENVIANDO]);

                if ($reivindicada === 0) {
                    return;
                }

                try {
                    $this->client->distribuiSenha(
                        (int) $outbox->unidade_id,
                        (int) $outbox->servico_id,
                        (int) $outbox->prioridade_id,
                        $outbox->cliente,
                        $outbox->metadata,
                    );

                    $outbox->update([
                        'status' => SenhaOutbox::STATUS_ENVIADO,
                        'enviado_em' => now(),
                        'erro' => null,
                    ]);

                    $enviadas++;
                } catch (NovosgaIndisponivelException $e) {
                    $tentativas = $outbox->tentativas + 1;
                    $esgotado = $tentativas >= $maxTentativas;

                    if ($esgotado) {
                        Log::critical("[outbox] senha {$outbox->senha_provisoria} esgotou tentativas ({$tentativas}) — intervenção manual necessária: {$e->getMessage()}");
                    }

                    $outbox->update([
                        'status' => $esgotado ? SenhaOutbox::STATUS_ESGOTADO : SenhaOutbox::STATUS_FALHOU,
                        'tentativas' => $tentativas,
                        'erro' => $e->getMessage(),
                    ]);
                } catch (\Throwable $e) {
                    // Erro não relacionado a indisponibilidade (ex.: validação
                    // rejeitada pelo NovoSGA) não se resolve sozinho — reenviar
                    // para sempre só gastaria chamadas de API sem chance de
                    // sucesso. Vai direto para o estado terminal.
                    Log::critical("[outbox] senha {$outbox->senha_provisoria} falhou de forma não recuperável — intervenção manual necessária: {$e->getMessage()}");

                    $outbox->update([
                        'status' => SenhaOutbox::STATUS_ESGOTADO,
                        'tentativas' => $outbox->tentativas + 1,
                        'erro' => $e->getMessage(),
                    ]);
                }
            });

        return $enviadas;
    }

    protected function contadorProvisorias(): int
    {
        return SenhaOutbox::query()
            ->whereDate('data_chegada', today())
            ->count();
    }
}
