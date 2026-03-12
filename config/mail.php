<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    */

    'default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    */

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
            'verify_peer' => env('MAIL_VERIFY_PEER', true),
        ],

        'mailgun' => [
            'transport' => 'mailgun',
            'domain' => env('MAILGUN_DOMAIN'),
            'secret' => env('MAILGUN_SECRET'),
            'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
            'scheme' => 'https',
        ],

        'postmark' => [
            'transport' => 'postmark',
            'token' => env('POSTMARK_TOKEN'),
        ],

        'resend' => [
            'transport' => 'resend',
            'key' => env('RESEND_KEY'),
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL', 'stack'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => ['mailgun', 'log'],
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => ['mailgun', 'postmark'],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@aukcije.ba'),
        'name' => env('MAIL_FROM_NAME', 'Aukcije.ba'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    */

    'markdown' => [
        'theme' => 'default',
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */

    'notifications' => [
        // Which notifications to send via email
        'outbid' => true,
        'auction_won' => true,
        'auction_ended' => true,
        'payment_received' => true,
        'item_shipped' => true,
        'dispute' => true,
        'kyc_status' => true,
        'payment_reminder' => true,
        'shipping_reminder' => true,
        
        // SMS notifications (via Infobip/Twilio)
        'sms' => [
            'enabled' => env('SMS_DRIVER', 'log') !== 'log',
            'outbid' => false, // Only for high-value auctions
            'auction_won' => true,
            'item_shipped' => true,
            'payment_reminder' => false,
        ],
        
        // Push notifications (via Firebase)
        'push' => [
            'enabled' => env('FIREBASE_PROJECT_ID') !== null,
            'outbid' => true,
            'auction_won' => true,
            'item_shipped' => true,
            'dispute' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Preferences
    |--------------------------------------------------------------------------
    */

    'preferences' => [
        // Default notification settings for new users
        'defaults' => [
            'email_outbid' => true,
            'email_auction_won' => true,
            'email_auction_ended' => true,
            'email_payment_received' => true,
            'email_item_shipped' => true,
            'email_dispute' => true,
            'email_kyc_status' => true,
            'email_payment_reminder' => true,
            'email_shipping_reminder' => true,
            
            'sms_outbid' => false,
            'sms_auction_won' => true,
            'sms_item_shipped' => true,
            
            'push_outbid' => true,
            'push_auction_won' => true,
            'push_item_shipped' => true,
            'push_dispute' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    */

    'queue' => [
        'connection' => 'redis',
        'queue' => 'emails',
    ],

];
