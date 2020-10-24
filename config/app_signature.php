<?php

return [
    "gateway" => env("SIGNATURE_GATEWAY", "esign_genie"),

    "eversign" => [
        "api_key" => env("EVERSIGN_API_KEY"),
        "business_id" => env("EVERSIGN_BUSINESS_ID")
    ],

    'hellosign' => [
        'api_key' => env('HELLOSIGN_API_KEY'),
        'client_id' => env('HELLOSIGN_CLIENT_ID')
    ],

    'esign_genie' => [
        'api_key' => env('ESIGN_GENIE_API_KEY'),
        'api_secret' => env('ESIGN_GENIE_API_SECRET'),
        "api_hash" => env('ESIGN_GENIE_SECRET_HASH'),
    ],
];
