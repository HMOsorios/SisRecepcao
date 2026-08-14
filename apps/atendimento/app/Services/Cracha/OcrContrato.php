<?php

namespace App\Services\Cracha;

/**
 * Ponto de extensão para leitura ótica de documentos (CPF/RG) no cadastro
 * rápido do visitante (Seção 2.3).
 *
 * A implementação real depende de hardware/scanner ou webcam + serviço de OCR
 * (Seção 9.7). Quando indisponível, o atendente usa a digitação manual — o
 * contrato retorna null e o fluxo segue com os dados informados na tela.
 */
interface OcrContrato
{
    /**
     * Tenta ler os dados do documento a partir de uma imagem (base64 ou path).
     *
     * @return array{numero?: string, nome?: string, tipo?: string}|null
     */
    public function lerDocumento(string $imagem): ?array;
}
