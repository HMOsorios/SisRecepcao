<?php

namespace App\Services\Cracha;

/**
 * Implementação provisória do contrato de OCR (sem integração real).
 *
 * Substituir por um adaptador real (scanner dedicado / webcam + serviço OCR)
 * quando o hardware chegar — Seção 9.7. Até lá, o cadastro usa digitação manual.
 */
class OcrNaoImplementado implements OcrContrato
{
    public function lerDocumento(string $imagem): ?array
    {
        return null;
    }
}
