<?php

namespace App\Services\Novosga;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Publica eventos no hub Mercure a partir do SisRecepção (opcional).
 *
 * O caminho principal de tempo real já existe no NovoSGA (notificaFilaUnidade,
 * notificaPainel, notificaAtendimento). Este publicador serve para eventos
 * próprios do SisRecepção (ex.: notificação de crachá no console do setor).
 */
class MercurePublisher
{
    public function __construct(
        protected readonly string $mercureUrl,
        protected readonly string $publisherKey,
        protected readonly string $jwt,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            mercureUrl: (string) config('mercure.url'),
            publisherKey: (string) config('mercure.publisher_key'),
            jwt: (string) config('mercure.jwt'),
        );
    }

    /**
     * @param  array<int,string>  $topics
     * @param  array<string,mixed>  $data
     */
    public function publish(array $topics, string $type, array $data = []): void
    {
        if (empty($topics) || ($this->publisherKey === '' && $this->jwt === '')) {
            return;
        }

        try {
            Http::timeout(3)
                ->withToken($this->jwt !== '' ? $this->jwt : $this->publisherKey)
                ->asForm()
                ->post($this->mercureUrl, [
                    'topic' => $topics,
                    'data' => json_encode([
                        'type' => $type,
                        'data' => $data,
                    ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                ])
                ->throw();
        } catch (\Throwable $e) {
            Log::warning('[mercure] falha ao publicar: '.$e->getMessage());
        }
    }
}
