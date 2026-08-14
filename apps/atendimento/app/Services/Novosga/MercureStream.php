<?php

namespace App\Services\Novosga;

/**
 * Helper para construir URLs de assinatura (EventSource/SSE) no hub Mercure.
 *
 * O painel de TV e o console do setor se conectam direto no Mercure do NovoSGA
 * (Seção 2.6/5.1): /unidades/{id}/painel, /unidades/{id}/fila e /atendimentos/{id}.
 */
class MercureStream
{
    public function __construct(
        protected readonly string $mercureUrl,
        protected readonly string $subscriberKey = '',
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            mercureUrl: (string) config('mercure.url'),
            subscriberKey: (string) config('mercure.subscriber_key'),
        );
    }

    /**
     * URL do EventSource para um conjunto de tópicos.
     *
     * @param  array<int,string>  $topics
     */
    public function eventSourceUrl(array $topics, ?string $token = null): string
    {
        $url = parse_url($this->mercureUrl);
        $porta = isset($url['port']) ? ':'.$url['port'] : '';
        $base = sprintf('%s://%s%s%s', $url['scheme'] ?? 'http', $url['host'] ?? '', $porta, $url['path'] ?? '');

        $query = [];
        foreach ($topics as $topic) {
            $query['topic'][] = $topic;
        }

        $token ??= $this->subscriberKey;
        if ($token !== '') {
            $query['token'] = $token;
        }

        return $base.'?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Tópico de painel de chamadas de uma unidade.
     */
    public function topicPainel(int $unidadeId): string
    {
        return "/unidades/{$unidadeId}/painel";
    }

    /**
     * Tópico de fila de uma unidade.
     */
    public function topicFilaUnidade(int $unidadeId): string
    {
        return "/unidades/{$unidadeId}/fila";
    }

    /**
     * Tópico de um atendimento específico.
     */
    public function topicAtendimento(int $atendimentoId): string
    {
        return "/atendimentos/{$atendimentoId}";
    }
}
