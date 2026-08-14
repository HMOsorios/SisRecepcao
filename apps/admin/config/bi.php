<?php

return [

    /*
    |--------------------------------------------------------------------------
    | BI e painel da diretoria (Seção 2.5/8.1)
    |--------------------------------------------------------------------------
    */

    /*
    | Unidade "Sede SMS" no NovoSGA (fonte dos indicadores).
    */
    'unidade_id' => (int) env('SMS_UNIDADE_ID', 1),

    /*
    | Amostra máxima de atendimentos carregados para agregar indicadores.
    */
    'amostra' => (int) env('BI_AMOSTRA', 500),
];
