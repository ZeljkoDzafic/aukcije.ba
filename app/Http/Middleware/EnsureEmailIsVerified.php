<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\EnsureEmailIsVerified as Middleware;

class EnsureEmailIsVerified extends Middleware
{
    /**
     * Redirect when the email is not verified.
     */
    public static function redirectTo($request): ?string
    {
        return $request->expectsJson() ? null : route('verification.notice');
    }
}
