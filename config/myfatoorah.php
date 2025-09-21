<?php

return [
    'api_key' => env('MYFATOORAH_API_KEY'),
    'base_url' => env('MYFATOORAH_BASE_URL', 'https://apitest.myfatoorah.com'),
    'currency' => env('MYFATOORAH_CURRENCY', 'SAR'),
    'mobile_number' => env('MYFATOORAH_MOBILE_NUMBER', '96512345678'),
    'webhook_secret_key' => env('MYFATOORAH_WEBHOOK_SECRET_KEY'),
    'webhook_url' => env('MYFATOORAH_WEBHOOK_URL', 'https://yourdomain.com/api/my_fatoorah/webhook'),
];
