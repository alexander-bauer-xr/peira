<?php

return [

    'enabled' => env('COOKIE_CONSENT_ENABLED', true),

    'cookie_name' => 'laravel_cookie_consent',

    'cookie_lifetime' => 365 * 20,

    'categories' => [
        'necessary'   => 'Notwendig (immer aktiv)',
        //'statistics'  => 'Statistiken',
        'external'    => 'Externe Medien',
    ],
];
