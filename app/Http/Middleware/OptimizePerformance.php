<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

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
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
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
