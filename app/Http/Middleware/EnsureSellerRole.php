<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerRole
{
    /**
     * Handle an incoming request.
     *
     * Only allow users with seller or verified_seller role.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->hasAnyRole(['seller', 'verified_seller'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Nemate dozvolu za ovu akciju. Potrebno je da imate seller nalog.',
                ], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Nemate dozvolu za ovu akciju. Potrebno je da imate seller nalog.');
        }

        return $next($request);
    }
}
