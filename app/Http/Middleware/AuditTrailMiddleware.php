<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\AdminLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * T-1103: Automatically audit all mutating admin/seller API requests.
 *
 * Apply to admin and seller route groups so every POST/PUT/PATCH/DELETE
 * is logged to admin_logs without manual calls in each controller.
 */
class AuditTrailMiddleware
{
    /** @var string[] Methods that mutate state */
    private const MUTATING_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! in_array($request->method(), self::MUTATING_METHODS, true)) {
            return $response;
        }

        $user = Auth::user();

        if (! $user) {
            return $response;
        }

        // Resolve target type/id from route parameters
        $routeParams = $request->route()->parameters();
        $targetType  = null;
        $targetId    = null;

        foreach (['auction', 'order', 'user', 'dispute', 'category'] as $param) {
            if (isset($routeParams[$param])) {
                $model      = $routeParams[$param];
                $targetType = $param;
                $targetId   = is_object($model) ? (string) $model->getKey() : (string) $model;
                break;
            }
        }

        try {
            AdminLog::create([
                'admin_id'    => $user->id,
                'action'      => $request->method() . ':' . $request->route()->getName(),
                'target_type' => $targetType,
                'target_id'   => $targetId,
                'metadata'    => [
                    'url'         => $request->url(),
                    'status_code' => $response->getStatusCode(),
                    'ip'          => $request->ip(),
                ],
            ]);
        } catch (\Throwable) {
            // Never let audit logging break the response
        }

        return $response;
    }
}
