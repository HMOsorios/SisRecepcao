<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Integração com o NovoSGA (leitura — BI e painel da diretoria)
    |--------------------------------------------------------------------------
    | O painel administrativo consome apenas operações de LEITURA da API do
    | NovoSGA (atendimentos, filas, catálogos). Nenhuma escrita é feita aqui —
    | a máquina de estados continua sob responsabilidade dos módulos de linha
    | de frente (apps/atendimento).
    */

    'base_url' => env('NOVOSGA_BASE_URL', 'http://novosga.test'),

    'client_id' => env('NOVOSGA_CLIENT_ID', 'sisrecepcao_admin'),
    'client_secret' => env('NOVOSGA_CLIENT_SECRET', ''),

    /*
    | Grant alternativo (password) para ambientes sem conta de serviço.
    */
    'username' => env('NOVOSGA_USERNAME', ''),
    'password' => env('NOVOSGA_PASSWORD', ''),

    'timeout' => (int) env('NOVOSGA_TIMEOUT', 5),
    'retries' => (int) env('NOVOSGA_RETRIES', 3),

    /*
    | Modo de simulação: devolve dados de demonstração quando o NovoSGA não
    | está disponível (desenvolvimento/demonstração).
    */
    'simulation' => env('NOVOSGA_SIMULATION', true),

    'token_cache_ttl' => 3600,
];
