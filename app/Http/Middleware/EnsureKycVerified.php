<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKycVerified
{
    /**
     * Handle an incoming request.
     *
     * Redirect users who haven't completed KYC verification.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Check if user has verified email
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('error', 'Molimo verifikujte svoj email prije nastavka.');
        }

        // Check KYC level for seller actions
        if ($user->hasAnyRole(['seller', 'verified_seller'])) {
            $kycLevel = $user->kycLevel();

            // Level 1: Email verified (basic)
            // Level 2: SMS verified (intermediate)
            // Level 3: Document verified (full)

            if ($kycLevel < 2) {
                return redirect()->route('kyc.verify')
                    ->with('error', 'Molimo kompletirajte KYC verifikaciju za prodaju.');
            }
        }

        return $next($request);
    }
}
