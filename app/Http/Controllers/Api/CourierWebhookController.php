<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CourierWebhookController extends Controller
{
    public function handle(Request $request, string $courier, ShippingService $shippingService): Response
    {
        $payload   = $request->getContent();
        $signature = $request->header('X-Courier-Signature', '');

        if (! $this->verifySignature($courier, $payload, $signature)) {
            Log::warning("Courier webhook signature verification failed for [{$courier}]");

            return response('Unauthorized', 401);
        }

        try {
            $shippingService->handleWebhook($courier, $request->all());
        } catch (\Throwable $e) {
            Log::error("Courier webhook handler failed for [{$courier}]", ['error' => $e->getMessage()]);

            return response('Internal Server Error', 500);
        }

        return response('OK', 200);
    }

    private function verifySignature(string $courier, string $payload, string $signature): bool
    {
        $secret = config("shipping.couriers.{$courier}.webhook_secret");

        if (empty($secret)) {
            // No secret configured — skip verification in non-production environments
            return app()->environment('production') === false;
        }

        return hash_equals(
            hash_hmac('sha256', $payload, $secret),
            $signature
        );
    }
}
