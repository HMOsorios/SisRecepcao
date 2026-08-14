<?php

namespace App\Services\Novosga;

use RuntimeException;

/**
 * Exceção genérica de comunicação com o NovoSGA.
 */
class NovosgaApiException extends RuntimeException
{
    public function __construct(string $message, public readonly ?int $status = null)
    {
        parent::__construct($message, $status ?? 0);
    }
}
