<?php

namespace App\Services\Novosga;

use RuntimeException;

/**
 * Erro na integração com a API do NovoSGA (resposta HTTP 4xx/erro de negócio).
 */
class NovosgaApiException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?int $status = null,
    ) {
        parent::__construct($message, $status ?? 0);
    }
}
