<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Keycloak — IdP central do SisRecepção (Seção 5.4/8.6)
    |--------------------------------------------------------------------------
    */

    'base_url' => env('KEYCLOAK_BASE_URL', 'http://keycloak.test'),
    'realm' => env('KEYCLOAK_REALM', 'sisrecepcao'),
    'client_id' => env('KEYCLOAK_CLIENT_ID', 'sisrecepcao-admin'),
    'client_secret' => env('KEYCLOAK_CLIENT_SECRET', ''),
    'redirect_uri' => env('KEYCLOAK_REDIRECT_URI', ''),

    'authorization_endpoint' => env('KEYCLOAK_AUTH_URL', ''),
    'token_endpoint' => env('KEYCLOAK_TOKEN_URL', ''),
    'userinfo_endpoint' => env('KEYCLOAK_USERINFO_URL', ''),
    'logout_endpoint' => env('KEYCLOAK_LOGOUT_URL', ''),

    /*
    | Escopos solicitados no fluxo authorization-code + PKCE.
    */
    'scopes' => 'openid profile email',

    /*
    | Credenciais de administrador do realm (gestão de usuários — Seção 8.3).
    | Deixar vazio desativa o painel de usuários.
    */
    'admin_user' => env('KEYCLOAK_ADMIN_USER', ''),
    'admin_password' => env('KEYCLOAK_ADMIN_PASSWORD', ''),

    /*
    | Nome do claim que identifica o usuário (subject).
    */
    'subject_claim' => 'sub',

    /*
    | Session keys usadas pela guarda/middleware.
    */
    'session_token_key' => 'keycloak_access_token',
    'session_refresh_key' => 'keycloak_refresh_token',
    'session_id_token_key' => 'keycloak_id_token',
    'session_user_key' => 'keycloak_user',
    'session_state_key' => 'keycloak_state',
];
