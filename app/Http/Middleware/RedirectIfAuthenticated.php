<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirect based on user role
                $user = Auth::guard($guard)->user();

                if ($user->hasAnyRole(['admin', 'super_admin', 'moderator'])) {
                    return redirect()->route('admin.dashboard');
                }

                if ($user->hasAnyRole(['seller', 'verified_seller'])) {
                    return redirect()->route('seller.dashboard');
                }

                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
