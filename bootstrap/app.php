<?php

declare(strict_types=1);

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EnsureAuctionActive;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\EnsureKycVerified;
use App\Http\Middleware\EnsureSellerRole;
use App\Http\Middleware\FeatureEnabled;
use App\Http\Middleware\HandleViewData;
use App\Http\Middleware\PreventShillBidding;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ThrottleBids;
use App\Http\Middleware\ValidateApiSignature;
use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\EventServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        AppServiceProvider::class,
        EventServiceProvider::class,
        AuthServiceProvider::class,
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
            HandleViewData::class,
            SetLocale::class,
            SecurityHeaders::class,
        ]);

        // API middleware
        $middleware->api(append: [
            EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            SubstituteBindings::class,
        ]);

        // Middleware aliases
        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'verified' => EnsureEmailIsVerified::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'kyc.verified' => EnsureKycVerified::class,
            'seller' => EnsureSellerRole::class,
            'throttle.bids' => ThrottleBids::class,
            'security.headers' => SecurityHeaders::class,
            'auction.active' => EnsureAuctionActive::class,
            'anti.shill' => PreventShillBidding::class,
            'api.signature' => ValidateApiSignature::class,
            'feature' => FeatureEnabled::class,
        ]);

        // Stateful API domains
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom exception handling
    })->create();
