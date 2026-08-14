<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sede da SMS
    |--------------------------------------------------------------------------
    | Dados da unidade "Sede SMS" usados pelos módulos de atendimento.
    */

    'unidade_id' => (int) env('SMS_UNIDADE_ID', 1),

    'nome' => env('SMS_NOME', 'Secretaria Municipal de Saúde'),

    /*
    | Duração padrão (minutos) da validade de um crachá temporário (Módulo 2.3).
    */
    'cracha_validade_minutos' => (int) env('SMS_CRACHA_VALIDADE_MINUTOS', 120),

    /*
    | Prazo de retenção (dias) de fotos/dados de visitantes antes do expurgo
    | automatizado (LGPD — Seção 2.5/9.6). Parâmetro configurável.
    */
    'retencao_visitantes_dias' => (int) env('SMS_RETENCAO_VISITANTES_DIAS', 30),
];
