<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mercure Hub (compartilhado com o NovoSGA)
    |--------------------------------------------------------------------------
    | O painel de TV e o console do atendente conectam direto via EventSource
    | (SSE) nos tópicos publicados pelo NovoSGA (Seção 5.1/2.6).
    */

    'url' => env('MERCURE_URL', 'http://mercure.test/.well-known/mercure'),

    /*
    | Chaves para publicar eventos a partir deste app (opcional).
    */
    'publisher_key' => env('MERCURE_PUBLISHER_KEY', ''),
    'subscriber_key' => env('MERCURE_SUBSCRIBER_KEY', ''),

    /*
    | JWT pré-gerado (quando usado como alternativa às chaves acima).
    */
    'jwt' => env('MERCURE_JWT', ''),

    /*
    | Intervalo de fallback de polling quando o SSE está indisponível (ms).
    */
    'poll_interval' => 8000,

    /*
    | Tempo máximo de espera do EventSource antes de reconectar (ms).
    */
    'reconnect_delay' => 5000,
];
