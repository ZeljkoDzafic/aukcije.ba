<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Imgix, AWS, payment gateways, etc.
    |
    */

    // -------------------------------------------------------------------------
    // Imgix CDN (image transformation & delivery)
    // Set IMGIX_DOMAIN in .env to enable CDN-based transformations.
    // Leave empty to serve images directly from S3 or local disk.
    // -------------------------------------------------------------------------
    'imgix' => [
        'domain' => env('IMGIX_DOMAIN', ''),
    ],

    // -------------------------------------------------------------------------
    // AWS S3
    // -------------------------------------------------------------------------
    'aws' => [
        'key'    => env('AWS_ACCESS_KEY_ID', ''),
        'secret' => env('AWS_SECRET_ACCESS_KEY', ''),
        'region' => env('AWS_DEFAULT_REGION', 'eu-central-1'),
        'bucket' => env('AWS_BUCKET', ''),
    ],

    // -------------------------------------------------------------------------
    // Firebase Cloud Messaging (push notifications — T-1402)
    // -------------------------------------------------------------------------
    'fcm' => [
        'server_key' => env('FCM_SERVER_KEY', ''),
    ],

    // -------------------------------------------------------------------------
    // Stripe payment gateway
    // -------------------------------------------------------------------------
    'stripe' => [
        'key'    => env('STRIPE_KEY', ''),
        'secret' => env('STRIPE_SECRET', ''),
    ],

];
