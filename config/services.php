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


    // Dans config/services.php
    'facebook' => [
        'app_id' => env('FACEBOOK_APP_ID'),
        'app_secret' => env('FACEBOOK_APP_SECRET'),
    ],

    'whatsapp' => [
        'token' => env('WHATSAPP_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
    ],

    'dexchange' => [
        'api_key' => env('DEXCHANGE_API_KEY'),
        'api_url' => env('DEXCHANGE_API_URL', 'https://api-m.dexchange.sn/api/v1/transaction/init'),
        'balance_url' => env('DEXCHANGE_BALANCE_URL', 'https://api.dexchange.com/api/v1/api-services/balance'),
        'sub_merchant_id' => env('DEXCHANGE_SUB_MERCHANT_ID'),
    ],

    'dexpay' => [
        'api_key' => env('DEXPAY_API_KEY'),       // pk_test_xxx ou pk_live_xxx
        'api_secret' => env('DEXPAY_API_SECRET'), // sk_test_xxx ou sk_live_xxx
        'api_url' => env('DEXPAY_API_URL', 'https://api.dexpay.africa/api/v1'),
        'sandbox_url' => env('DEXPAY_SANDBOX_URL', 'https://api-sandbox.dexpay.africa/api/v1'),
        'sandbox' => env('DEXPAY_SANDBOX', false),
    ],

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
