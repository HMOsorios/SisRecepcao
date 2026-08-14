<?php

return [

    /*
    | Campo honeypot escondido (bots o preenchem).
    */
    'field' => 'website',

    /*
    | Campo de tempo de preenchimento (timestamp do carregamento).
    */
    'tempo_field' => 'empresa',

    /*
    | Bloqueia submissões em menos de N segundos após o carregamento.
    */
    'validar_tempo' => true,

    'tempo_minimo' => 3,
];
