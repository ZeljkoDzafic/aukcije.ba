<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Share common view data (flash messages, auth, feature flags)
 * with all Blade/Livewire views.
 */
class HandleViewData
{
    public function handle(Request $request, Closure $next): Response
    {
        view()->share([
            'appName'      => config('app.name'),
            'featureFlags' => [
                'proxyBidding' => config('auction.proxy_bidding_enabled'),
                'antiSniping'  => config('auction.auto_extension_enabled'),
                'escrow'       => config('escrow.enabled'),
            ],
        ]);

        return $next($request);
    }
}
