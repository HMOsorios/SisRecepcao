<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dados institucionais da SMS (Seção 8.1/8.4)
    |--------------------------------------------------------------------------
    | Valores padrão; podem ser editados no painel de Configurações (Developer).
    */

    'nome' => env('SMS_NOME', 'Secretaria Municipal de Saúde'),
    'sigla' => env('SMS_SIGLA', 'SMS'),
    'identidade' => env('SMS_IDENTIDADE', 'Prefeitura Municipal'),

    /*
    | Unidade "Sede SMS" no NovoSGA (indicadores de BI).
    */
    'unidade_id' => (int) env('SMS_UNIDADE_ID', 1),

    /*
    | Contatos institucionais (rodapé/páginas legais).
    */
    'contato' => [
        'endereco' => env('SMS_ENDERECO', ''),
        'telefone' => env('SMS_TELEFONE', ''),
        'email' => env('SMS_EMAIL', ''),
    ],

    /*
    | Links de acesso a outros sistemas da SMS (Seção 8.2).
    */
    'sistemas' => [
        ['nome' => 'SisPec', 'url' => env('SIS_PEC_URL', '')],
        ['nome' => 'SEI', 'url' => env('SEI_URL', '')],
        ['nome' => 'TI Conecta', 'url' => env('TI_CONECTA_URL', '')],
        ['nome' => 'SisEscala', 'url' => env('SIS_ESCALA_URL', '')],
    ],

    /*
    | Repositório GitHub usado no painel de manutenção (pull/deploy).
    */
    'github_repo' => env('GITHUB_REPO', 'HMOsorios/sisrecepcao'),

    /*
    | Webhook de deploy do Coolify (Seção 8.4/8.9).
    */
    'coolify_webhook_url' => env('COOLIFY_WEBHOOK_URL', ''),
];
