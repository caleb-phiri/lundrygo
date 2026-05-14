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
// Add to config/services.php
'google' => [
    'maps_api_key' => env('GOOGLE_MAPS_API_KEY', ''),
],

'mapbox' => [
    'access_token' => env('MAPBOX_ACCESS_TOKEN', ''),
],

'map' => [
    'provider' => env('MAP_PROVIDER', 'google'), // google, mapbox
    'use_fallback' => env('MAP_USE_FALLBACK', true),
],

'delivery' => [
    'base_fee' => env('DELIVERY_BASE_FEE', 5.00),
    'per_km_rate' => env('DELIVERY_PER_KM_RATE', 1.50),
    'min_fee' => env('DELIVERY_MIN_FEE', 5.00),
    'max_fee' => env('DELIVERY_MAX_FEE', 25.00),
    'express_multiplier' => env('DELIVERY_EXPRESS_MULTIPLIER', 1.5),
],
'twilio' => [
    'sid' => env('TWILIO_SID'),
    'token' => env('TWILIO_TOKEN'),
    'from_number' => env('TWILIO_FROM_NUMBER'),
],

'firebase' => [
    'enabled' => env('FIREBASE_ENABLED', false),
    'credentials_path' => env('FIREBASE_CREDENTIALS_PATH'),
],
];
