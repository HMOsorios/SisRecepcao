<?php

namespace App\Services\Notificacoes;

use App\Models\Notificacao;
use App\Services\Novosga\MercurePublisher;
use App\Services\Novosga\MercureStream;
use Illuminate\Database\Eloquent\Collection;

/**
 * Ponte Mercure → pop-up/som no console do setor (Seção 2.6, passo 2).
 *
 * A chegada do visitante é notificada em tempo real para o departamento de
 * destino: persistimos a notificação no sisrecepcao_db e publicamos um evento
 * no tópico de fila da unidade, que o console filtra por departamento_id.
 */
class NotificacaoService
{
    public function __construct(
        protected readonly MercurePublisher $publisher,
        protected readonly MercureStream $stream,
    ) {}

    /**
     * Registra e dispara a notificação de chegada ao setor de destino.
     *
     * @param  array<string,mixed>  $payload
     */
    public function notificarChegada(int $departamentoId, array $payload, ?int $atendimentoId = null): Notificacao
    {
        $notificacao = Notificacao::create([
            'atendimento_id' => $atendimentoId,
            'departamento_id' => $departamentoId,
            'tipo' => 'visita',
            'payload' => $payload,
        ]);

        $this->publisher->publish(
            [$this->stream->topicFilaUnidade($departamentoId)],
            'visita',
            $notificacao->toArray(),
        );

        return $notificacao;
    }

    /**
     * Lista as notificações de um setor (console do atendente).
     *
     * @return Collection<int, Notificacao>
     */
    public function listarPorSetor(int $departamentoId, bool $apenasNaoLidas = false)
    {
        return Notificacao::query()
            ->where('departamento_id', $departamentoId)
            ->when($apenasNaoLidas, fn ($q) => $q->whereNull('lida_em'))
            ->latest()
            ->limit(50)
            ->get();
    }

    public function marcarLida(int $id): void
    {
        Notificacao::query()->whereKey($id)->first()?->marcarComoLida();
    }
}
