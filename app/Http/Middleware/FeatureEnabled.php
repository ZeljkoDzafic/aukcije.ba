<?php

namespace App\Http\Middleware;

use App\Models\FeatureFlag;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureEnabled
{
    public function handle(Request $request, Closure $next, string $flag): Response
    {
        if (! FeatureFlag::isActive($flag)) {
            abort(404);
        }

        return $next($request);
    }
}
