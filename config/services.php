<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'ultramsg' => [
        'token' => env('ULTRAMSG_TOKEN'),
        'instance_id' => env('ULTRAMSG_INSTANCE_ID'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'whatsapp' => [
        // Meta WhatsApp Business API settings
        'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'business_account_id' => env('BUSINESS_ACCOUNT_ID'),
        'api_version' => env('WHATSAPP_API_VERSION', 'v16.0'),
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN'),

        // Provider selection for notifications
        'default_provider' => env('WHATSAPP_DEFAULT_PROVIDER', 'ultramsg'),

        // // Template configuration
        // 'templates' => [
        //     'otp' => [
        //         'expiry_minutes' => env('OTP_EXPIRY_MINUTES', 2),
        //     ],
        // ],
    ],

    'twilio' => [
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),
    ],
    'fcm' => [
        'key_file' => env('GOOGLE_APPLICATION_CREDENTIALS', 'disciplinary-dee3b-d19a8a59ac1e.json'),
        'project_id' => env('FCM_PROJECT_ID', 'disciplinary-dee3b'),
        'server_key' => env('FCM_SERVER_KEY', ''),

        // Add the firebase web configuration
        'api_key' => env('FIREBASE_API_KEY', 'AIzaSyBNGAsSVJwHANv1s5ycDHEV7dOPPlnfSLw'),
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN', 'disciplinary-dee3b.firebaseapp.com'),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', 'disciplinary-dee3b.firebasestorage.app'),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID', '598587357589'),
        'app_id' => env('FIREBASE_APP_ID', '1:598587357589:web:1eed8ca760763585179e9c'),
        'measurement_id' => env('FIREBASE_MEASUREMENT_ID', 'G-L1EKMBY7BR'),
        'vapid_key' => env('FIREBASE_VAPID_KEY', 'BLDHbN2MyU7DqPhXaKK29pm_eES1De91Ysl1_T2U6f0dFB_vLxqIi66LmHFAejQtoGFOfqi8pEoO8sWzTYUJsUc'),
    ],

];
