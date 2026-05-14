<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Payment Provider
    |--------------------------------------------------------------------------
    */
    'default_provider' => env('PAYMENT_PROVIDER', 'stripe'),
    
    /*
    |--------------------------------------------------------------------------
    | Payment Providers Configuration
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'stripe' => [
            'secret_key' => env('STRIPE_SECRET_KEY'),
            'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'currency' => 'usd',
        ],
        
        'paypal' => [
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
            'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
            'mode' => env('PAYPAL_MODE', 'sandbox'),
        ],
        
        'razorpay' => [
            'key_id' => env('RAZORPAY_KEY_ID'),
            'key_secret' => env('RAZORPAY_KEY_SECRET'),
            'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    */
    'currency' => env('PAYMENT_CURRENCY', 'USD'),
    'tax_rate' => env('PAYMENT_TAX_RATE', 0),
    'service_fee' => env('PAYMENT_SERVICE_FEE', 0),
    
    /*
    |--------------------------------------------------------------------------
    | Refund Settings
    |--------------------------------------------------------------------------
    */
    'refund_window_days' => env('REFUND_WINDOW_DAYS', 30),
    'auto_refund_on_cancellation' => env('AUTO_REFUND_ON_CANCELLATION', true),
];