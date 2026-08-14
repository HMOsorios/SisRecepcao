<?php

namespace App\Http\Controllers\PainelTv;

use App\Http\Controllers\Controller;
use App\Services\Novosga\MercureStream;
use App\Services\Novosga\NovosgaClient;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Módulo 2.2 — Painel de chamada (Smart TV / digital signage) com Mercure.
 */
class PainelTvController extends Controller
{
    public function __construct(
        protected readonly NovosgaClient $client,
        protected readonly MercureStream $mercure,
    ) {}

    public function index(): View
    {
        $unidadeId = (int) config('sms.unidade_id', 1);

        return view('painel-tv.painel', [
            'unidadeId' => $unidadeId,
            'mercureUrl' => $this->mercure->eventSourceUrl([
                $this->mercure->topicPainel($unidadeId),
                '/paineis',
            ]),
            'dadosUrl' => route('painel.dados'),
            'pollInterval' => config('mercure.poll_interval'),
            'reconnectDelay' => config('mercure.reconnect_delay'),
        ]);
    }

    /**
     * JSON das últimas senhas chamadas (fallback de polling + carga inicial).
     */
    public function dados(): JsonResponse
    {
        $unidadeId = (int) config('sms.unidade_id', 1);

        try {
            $senhas = $this->client->painel($unidadeId);
        } catch (\Throwable) {
            $senhas = [];
        }

        return response()->json($this->normalizar($senhas));
    }

    /**
     * Normaliza o payload do NovoSGA (PainelSenha) para o formato da TV.
     *
     * @param  array<int,mixed>  $senhas
     */
    protected function normalizar(array $senhas): array
    {
        return array_map(function (mixed $item): array {
            $item = is_array($item) ? $item : [];

            return [
                'id' => (int) ($item['id'] ?? 0),
                'senha' => $this->normalizarSenha($item['senha'] ?? ''),
                'sigla' => (string) ($item['sigla'] ?? ''),
                'numero' => (int) ($item['numero'] ?? 0),
                'local' => (string) ($item['local'] ?? ''),
                'numeroLocal' => (int) ($item['numeroLocal'] ?? 0),
                'servico' => is_array($item['servico'] ?? null)
                    ? (string) ($item['servico']['nome'] ?? '')
                    : (string) ($item['servico'] ?? ''),
                'prioridade' => (string) ($item['prioridade'] ?? ''),
                'peso' => (int) ($item['peso'] ?? 0),
                'cliente' => (string) (($item['cliente']['nome'] ?? $item['nome'] ?? '')),
            ];
        }, $senhas);
    }

    /**
     * A senha do NovoSGA pode vir como objeto {sigla, numero} ou string pronta.
     */
    protected function normalizarSenha(mixed $senha): string
    {
        if (is_array($senha)) {
            $sigla = (string) ($senha['sigla'] ?? '');
            $numero = str_pad((string) ($senha['numero'] ?? ''), 2, '0', STR_PAD_LEFT);

            return trim(trim($sigla.' '.$numero));
        }

        return (string) $senha;
    }
}
