<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request, string $gateway, PaymentService $paymentService): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Payment-Signature', '');

        if (! $paymentService->verifyWebhook($gateway, $payload, $signature)) {
            Log::warning("Payment webhook signature verification failed for [{$gateway}]");

            return response('Unauthorized', 401);
        }

        try {
            $paymentService->handleWebhook($gateway, $request->all());
        } catch (\Throwable $e) {
            Log::error("Payment webhook handler failed for [{$gateway}]", ['error' => $e->getMessage()]);

            return response('Internal Server Error', 500);
        }

        return response('OK', 200);
    }
}
