<?php

return [
    'bpi' => [
        'client_id' => env('BPI_CLIENT_ID'),
        'client_secret' => env('BPI_CLIENT_SECRET'),
        'redirect' => env('BPI_REDIRECT'),
        'auth_uri' => env('BPI_AUTH_URI', 'https://testoauth.bpi.com.ph/bpi/api/'),
        'api_gateway_uri' => env('BPI_API_GATEWAY_URI', 'https://apitest.bpi.com.ph/bpi/api/'),
        'auth_proxy' => env('BPI_AUTH_PROXY'),

        /**
         * You may use storage_path() here
         */
        'public_key' => __DIR__ . env('APP_PUBLIC_KEY'),
        'private_key' => __DIR__ . env('APP_PRIVATE_KEY'),
        'sender_certificate' => __DIR__ . env('SENDER_PUBLIC_KEY'),
    ],

    'unionbank' => [
        'client_id' => env('UNIONBANK_CLIENT_ID'),
        'client_secret' => env('UNIONBANK_CLIENT_SECRET'),
        'redirect' => env('UNIONBANK_REDIRECT'),
        'uri' => env('UNIONBANK_URI', 'https://api-uat.unionbankph.com/'),
        'partner_id' => env('UNIONBANK_PARTNER_ID'),
    ],
];
