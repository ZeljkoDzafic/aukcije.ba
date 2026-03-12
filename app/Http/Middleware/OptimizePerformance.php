<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * ===================================
 * PERFORMANCE OPTIMIZATION MIDDLEWARE
 * ===================================
 * Caching and query optimization
 */
class OptimizePerformance
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        // Add caching headers for static content
        $response = $next($request);

        // Cache static assets
        if ($request->is('build/*') || $request->is('vendor/*')) {
            $response->headers->add([
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }

        return $response;
    }
}
