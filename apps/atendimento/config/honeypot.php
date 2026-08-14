<?php

return [

    /*
    | Campo honeypot escondido (bots o preenchem). Nome deliberadamente fora
    | do dicionário comum de autofill (evitar "website", "empresa", "email",
    | "phone" etc. — autofill/gerenciadores de senha tentam preenchê-los
    | mesmo em campos type="hidden", derrubando usuários reais).
    */
    'field' => 'campo_verificacao_hp',

    /*
    | Campo de tempo de preenchimento (timestamp do carregamento).
    */
    'tempo_field' => 'marcador_tempo_hp',

    /*
    | Bloqueia submissões em menos de N segundos após o carregamento.
    */
    'validar_tempo' => true,

    'tempo_minimo' => 3,
];
