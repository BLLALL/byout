<?php

return [
    'test' => [
        'api_url' => env('FATORA_TEST_API_URL', 'https://egate-t.fatora.me/api/'),
        'merchant_portal' => env('FATORA_TEST_MERCHANT_PORTAL', 'https://fmp-t.fatora.me'),
        'username' => env('FATORA_TEST_USERNAME'),
        'password' => env('FATORA_TEST_PASSWORD'),
    ],
    'production' => [
        'api_url' => env('FATORA_PROD_API_URL', 'https://egate.fatora.me/api/'),
        'merchant_portal' => env('FATORA_PROD_MERCHANT_PORTAL', 'https://fmp.fatora.me'),
        'username' => env('FATORA_PROD_USERNAME'),
        'password' => env('FATORA_PROD_PASSWORD'),
    ],
    'environment' => env('FATORA_ENV', 'test'), // 'test' or 'production'
];
