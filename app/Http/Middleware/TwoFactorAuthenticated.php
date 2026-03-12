<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * T-1106: Enforce 2FA for roles that require it (verified_seller, admin, moderator).
 *
 * If the authenticated user has a 2FA secret but hasn't confirmed it in this session,
 * reject the request with 403 until they pass the 2FA challenge.
 */
class TwoFactorAuthenticated
{
    /** @var string[] Roles that must have 2FA enrolled and confirmed */
    private const REQUIRED_ROLES = ['verified_seller', 'admin', 'moderator'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        $requiresTwoFactor = collect(self::REQUIRED_ROLES)
            ->contains(fn (string $role) => $user->hasRole($role));

        if (! $requiresTwoFactor) {
            return $next($request);
        }

        // Has 2FA secret but not yet confirmed in this session
        if ($user->two_factor_secret && ! $user->two_factor_confirmed_at) {
            return response()->json([
                'error' => [
                    'code'    => '2FA_REQUIRED',
                    'message' => 'Two-factor authentication must be confirmed before proceeding.',
                ],
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
