<?php

namespace App\Services\Protocolo;

/**
 * Ponto de extensão preparado para futura integração com o SEI (Seção 7.4).
 *
 * A SMS opera SEI internamente, mas o módulo "Entrega de Documentos/Protocolo"
 * (Seção 2.1) NÃO integra com o SEI nesta fase — o protocolo é apenas uma
 * categoria de fila no NovoSGA/totem.
 *
 * Quando for decidido integrar, implemente este contrato em um adaptador que
 * chame o webservice próprio do SEI para abrir um processo a partir de um
 * Atendimento "Entrega de Documentos". Nenhum código de integração deve ser
 * escrito antes disso.
 */
interface ProtocoloContrato
{
    /**
     * Abre um processo/protocolo no SEI para um atendimento de entrega.
     *
     * @param  array<string,mixed>  $atendimento  (resposta do POST /api/distribui)
     * @return array<string,mixed>
     */
    public function abrirProcesso(array $atendimento): array;

    /**
     * Consulta o andamento de um processo no SEI.
     *
     * @return array<string,mixed>
     */
    public function consultar(string $numeroProcesso): array;
}
