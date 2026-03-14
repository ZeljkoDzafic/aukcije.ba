<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sentry Configuration
    |--------------------------------------------------------------------------
    |
    | Sentry is an error tracking and performance monitoring tool.
    | https://docs.sentry.io/platforms/php/configuration/laravel/
    |
    */

    'dsn' => env('SENTRY_LARAVEL_DSN', null),

    // Capture 20% of transactions for performance monitoring
    'traces_sample_rate' => env('SENTRY_TRACES_SAMPLE_RATE', 0.2),

    // Capture 10% of profiles for profiling
    'profiles_sample_rate' => env('SENTRY_PROFILES_SAMPLE_RATE', 0.1),

    // Error types to exclude from reporting
    'ignore_exceptions' => [
        // Don't report these exceptions
        'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
        'Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException',
        'Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException',
        'Illuminate\Auth\AuthenticationException',
        'Illuminate\Validation\ValidationException',
    ],

    // Before send callback
    'before_send' => function ($event) {
        // Add custom context
        $event->setTag('environment', config('app.env'));
        $event->setTag('version', config('app.version', '1.0.0'));
        
        return $event;
    },

    // Before send transaction callback
    'before_send_transaction' => function ($transaction) {
        // Filter out healthy transactions
        if ($transaction->getOp() === 'http.server' && $transaction->getStatus() === 200) {
            // Only keep 10% of successful requests
            if (rand(1, 100) > 10) {
                return null;
            }
        }
        
        return $transaction;
    },

];
