<?php

namespace App\Services\Totem;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Gera QR Code para o ticket de acompanhamento pelo smartphone (Seção 2.1).
 *
 * A URL aponta para o próprio SisRecepção (acompanhamento da fila); nenhum
 * serviço externo é consultado — funciona também em contingência (Seção 9.1).
 */
class QrCodeService
{
    /**
     * QR Code como data-URI (base64) pronto para <img src>.
     */
    public function dataUri(string $conteudo, int $tamanho = 220): string
    {
        $builder = new Builder(
            writer: new PngWriter,
            data: $conteudo,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $tamanho,
            margin: 8,
        );

        return $builder->build()->getDataUri();
    }

    /**
     * URL pública de acompanhamento de um ticket.
     */
    public function urlAcompanhamento(string $ticketId): string
    {
        return url('/totem/ticket/'.urlencode($ticketId));
    }
}
