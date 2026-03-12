<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiSignature
{
    public function handle(Request $request, Closure $next, string $secret = 'WEBHOOK_SECRET'): Response
    {
        $signature = $request->header('X-Signature');
        $payload   = $request->getContent();
        $expected  = hash_hmac('sha256', $payload, config("services.webhooks.{$secret}", ''));

        if (!hash_equals($expected, (string) $signature)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        return $next($request);
    }
}
