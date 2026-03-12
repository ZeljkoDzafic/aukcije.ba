<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\EnsureEmailIsVerified as Middleware;
class EnsureEmailIsVerified extends Middleware
{
    /**
     * Redirect when the email is not verified.
     *
     * @param mixed $request
     */
    public static function redirectTo($request): ?string
    {
        return method_exists($request, 'expectsJson') && $request->expectsJson()
            ? null
            : route('verification.notice');
    }
}
