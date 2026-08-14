<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Integração com o NovoSGA (motor de filas)
    |--------------------------------------------------------------------------
    | Modelo 1 (Seção 4.1/5.4 do recepcao.md): o NovoSGA é o back-end core de
    | filas e este app é a camada de orquestração que consome sua API REST.
    |
    */

    'base_url' => env('NOVOSGA_BASE_URL', 'http://novosga.test'),

    /*
    | Credenciais OAuth2 do NovoSGA.
    | A conta de serviço (client_credentials) representa o SisRecepção;
    | o atendente real é preservado via AtendimentoMeta (namespace sisrecepcao).
    */
    'client_id' => env('NOVOSGA_CLIENT_ID', 'sisrecepcao_atendimento'),
    'client_secret' => env('NOVOSGA_CLIENT_SECRET', ''),

    /*
    | Grant alternativo (password) — usado apenas quando o NovoSGA estiver
    | configurado para credenciais de usuário em vez de conta de serviço.
    */
    'username' => env('NOVOSGA_USERNAME', ''),
    'password' => env('NOVOSGA_PASSWORD', ''),

    /*
    | Timeout (segundos) e número de tentativas por requisição.
    */
    'timeout' => (int) env('NOVOSGA_TIMEOUT', 5),
    'retries' => (int) env('NOVOSGA_RETRIES', 3),

    /*
    | Modo de simulação (desenvolvimento/demonstração): quando o NovoSGA não
    | está disponível, o sistema emite senha provisória local e grava no outbox
    | (Seção 9.1). Desative em produção com NOVOSGA_SIMULATION=false.
    */
    'simulation' => env('NOVOSGA_SIMULATION', false),

    /*
    | Prefixo das senhas provisórias emitidas em modo de contingência.
    */
    'provisional_prefix' => env('NOVOSGA_PROVISIONAL_PREFIX', 'OFF'),

    /*
    | Cache do token OAuth2 (em segundos).
    */
    'token_cache_ttl' => 3600,

    /*
    | Limite de tentativas de reenvio de uma senha provisória (outbox,
    | Seção 9.1) antes de marcá-la como "esgotado" e parar de retentar
    | automaticamente — evita retry infinito de linha "poison pill".
    */
    'outbox_max_tentativas' => (int) env('NOVOSGA_OUTBOX_MAX_TENTATIVAS', 8),
];
