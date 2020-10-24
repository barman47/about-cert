<?php

return [
    'gateway' => env("APP_PAYMENT_GATEWAY", "rave"),

    'rave' => [
        'public_key' => env('RAVE_PUBLIC_KEY'),
        'secret_key' => env('RAVE_SECRET_KEY'),
        'encryption_key' => env('RAVE_ENCRYPTION_KEY'),
        'secret_hash' => env('RAVE_SECRET_HASH')
    ],
];
