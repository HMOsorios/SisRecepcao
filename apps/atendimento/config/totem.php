<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Totem de autoatendimento (Módulo 2.1)
    |--------------------------------------------------------------------------
    | O catálogo real de serviços/prioridades é gerido no NovoSGA e consumido
    | via API. Estes valores são o catálogo de FALLBACK usado quando o NovoSGA
    | está indisponível (modo de contingência — Seção 9.1), evitando totem vazio.
    |
    | Para produção, mantenha sincronizado com os IDs do NovoSGA.
    */

    'services' => [
        ['id' => 1, 'nome' => 'Atendimento Geral', 'sigla' => 'AG', 'icone' => 'atendimento'],
        ['id' => 2, 'nome' => 'Entrega de Documentos / Protocolo', 'sigla' => 'ED', 'icone' => 'documentos'],
        ['id' => 3, 'nome' => 'Servidores (RH / Folha)', 'sigla' => 'RH', 'icone' => 'servidores'],
        ['id' => 4, 'nome' => 'Fornecedores (Financeiro / Pagamentos)', 'sigla' => 'FN', 'icone' => 'fornecedores'],
        ['id' => 5, 'nome' => 'Ouvidoria', 'sigla' => 'OU', 'icone' => 'ouvidoria'],
        ['id' => 6, 'nome' => 'Gabinete', 'sigla' => 'GB', 'icone' => 'gabinete'],
        ['id' => 7, 'nome' => 'Transporte (TFD / Remoções)', 'sigla' => 'TR', 'icone' => 'transporte'],
        ['id' => 8, 'nome' => 'Controle e Avaliações', 'sigla' => 'CA', 'icone' => 'avaliacoes'],
    ],

    /*
    | Prioridades legais de fallback (peso/cor — NovoSGA Seção 5.1).
    */
    'priorities' => [
        ['id' => 1, 'nome' => 'Convencional', 'peso' => 0, 'cor' => '#1f6fb2'],
        ['id' => 2, 'nome' => 'Idoso (60+)', 'peso' => 100, 'cor' => '#c0392b'],
        ['id' => 3, 'nome' => 'Gestante', 'peso' => 100, 'cor' => '#c0392b'],
        ['id' => 4, 'nome' => 'Pessoa com Deficiência', 'peso' => 100, 'cor' => '#c0392b'],
        ['id' => 5, 'nome' => 'Autista (TEA)', 'peso' => 100, 'cor' => '#7d3c98'],
    ],

    /*
    | Acessibilidade (Seção 2.1): leitura em áudio ativada por padrão.
    */
    'audio_habilitado' => true,

    /*
    | Limite de emissões por minuto por IP (rate limiting — Seção 8.8).
    */
    'rate_limit' => (int) env('TOTEM_RATE_LIMIT', 12),
];
