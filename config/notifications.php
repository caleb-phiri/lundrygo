<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Channels
    |--------------------------------------------------------------------------
    */
    'channels' => [
        'email' => env('NOTIFICATION_EMAIL_ENABLED', true),
        'sms' => env('NOTIFICATION_SMS_ENABLED', false),
        'push' => env('NOTIFICATION_PUSH_ENABLED', true),
        'database' => env('NOTIFICATION_DATABASE_ENABLED', true),
        'webhook' => env('NOTIFICATION_WEBHOOK_ENABLED', false),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Webhook URL
    |--------------------------------------------------------------------------
    */
    'webhook_url' => env('NOTIFICATION_WEBHOOK_URL'),
    
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limit' => [
        'enabled' => true,
        'max_attempts' => 100,
        'decay_minutes' => 60,
    ],
];