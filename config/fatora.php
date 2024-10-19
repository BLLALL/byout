<?php

return [
    'test' => [
        'api_url' => env('FATORA_TEST_API_URL', 'https://egate-t.fatora.me/api/'),
        'merchant_portal' => env('FATORA_TEST_MERCHANT_PORTAL', 'https://fmp-t.fatora.me'),
        'username' => env('FATORA_TEST_USERNAME'),
        'password' => env('FATORA_TEST_PASSWORD'),
        'terminal_id' => env('FATORA_TEST_TERMINAL_ID'),
    ],
    'production' => [
        'api_url' => env('FATORA_PROD_API_URL', 'https://egate.fatora.me/api/'),
        'merchant_portal' => env('FATORA_PROD_MERCHANT_PORTAL', 'https://fmp.fatora.me'),
        'username' => env('FATORA_PROD_USERNAME'),
        'password' => env('FATORA_PROD_PASSWORD'),
        'terminal_id' => env('FATORA_PROD_TERMINAL_ID'),
    ],
    'environment' => env('FATORA_ENV', 'production'), // 'test' or 'production'
];
