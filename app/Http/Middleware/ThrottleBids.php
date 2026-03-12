<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ThrottleBids
{
    /**
     * Handle an incoming request.
     *
     * Rate limit bidding endpoints to prevent bid flooding.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 10, int $decayMinutes = 1): Response
    {
        $limiter = app(RateLimiter::class);
        
        $key = 'bid:'.$request->user()->id;
        
        if ($limiter->tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = $limiter->availableIn($key);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Previše pokušaja licitiranja. Pokušajte ponovo za ' . $retryAfter . ' sekundi.',
                ], 429);
            }

            return redirect()->back()
                ->with('error', 'Previše pokušaja licitiranja. Pokušajte ponovo za ' . $retryAfter . ' sekundi.')
                ->header('Retry-After', $retryAfter);
        }

        $limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers to response
        $remaining = $maxAttempts - $limiter->attempts($key);
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remaining,
        ]);

        return $response;
    }
}
