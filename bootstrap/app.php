<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \App\Providers\AppServiceProvider::class,
        \App\Providers\EventServiceProvider::class,
        \App\Providers\AuthServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Web middleware — share view data (feature flags, app name) globally
        $middleware->web(append: [
            \App\Http\Middleware\HandleViewData::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // API middleware
        $middleware->api(append: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Middleware aliases
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'kyc.verified' => \App\Http\Middleware\EnsureKycVerified::class,
            'seller' => \App\Http\Middleware\EnsureSellerRole::class,
            'throttle.bids'  => \App\Http\Middleware\ThrottleBids::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'auction.active'   => \App\Http\Middleware\EnsureAuctionActive::class,
            'anti.shill'       => \App\Http\Middleware\PreventShillBidding::class,
            'api.signature'    => \App\Http\Middleware\ValidateApiSignature::class,
            'feature'          => \App\Http\Middleware\FeatureEnabled::class,
        ]);

        // Stateful API domains
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom exception handling
    })->create();
