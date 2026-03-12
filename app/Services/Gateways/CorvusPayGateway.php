<?php

declare(strict_types=1);

namespace App\Services\Gateways;

use Exception;
use Illuminate\Support\Facades\Http;

class CorvusPayGateway implements PaymentGatewayInterface
{
    protected string $storeId;

    protected string $secretKey;

    protected string $webhookSecret;

    protected string $currency;

    protected string $apiUrl;

    public function __construct()
    {
        $this->storeId = config('payment.corvuspay.store_id');
        $this->secretKey = config('payment.corvuspay.secret_key');
        $this->webhookSecret = config('payment.corvuspay.webhook_secret');
        $this->currency = config('payment.corvuspay.currency', 'EUR');
        $this->apiUrl = config('payment.corvuspay.api_url');
    }

    public function processPayment(float $amount, array $data): array
    {
        try {
            $orderId = $data['order_id'] ?? uniqid('ORD_');
            $amountInCents = (int) ($amount * 100);

            // Generate CorvusPay signature
            $signature = $this->generateSignature([
                'store_id' => $this->storeId,
                'amount' => $amountInCents,
                'order_id' => $orderId,
                'currency' => $this->currency,
            ]);

            // Prepare payment request
            $paymentData = [
                'store_id' => $this->storeId,
                'amount' => $amountInCents,
                'order_id' => $orderId,
                'currency' => $this->currency,
                'description' => $data['description'] ?? 'Aukcije.ba Payment',
                'signature' => $signature,
                'success_url' => $data['success_url'] ?? route('payment.success'),
                'cancel_url' => $data['cancel_url'] ?? route('payment.cancel'),
                'notify_url' => route('webhooks.corvuspay'),
                'locale' => $data['locale'] ?? 'hr',
            ];

            // In production, send request to CorvusPay API
            // $response = Http::post($this->apiUrl, $paymentData);

            return [
                'success' => true,
                'transaction_id' => 'corvus_'.$orderId,
                'gateway' => 'corvuspay',
                'status' => 'pending',
                'redirect_url' => $this->apiUrl,
                'form_fields' => $paymentData,
                'method' => 'POST',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Payment failed: '.$e->getMessage(),
                'gateway' => 'corvuspay',
            ];
        }
    }

    public function refund(string $transactionId, ?float $amount = null): array
    {
        try {
            $refundData = [
                'store_id' => $this->storeId,
                'original_order_id' => str_replace('corvus_', '', $transactionId),
                'amount' => $amount ? (int) ($amount * 100) : null,
                'refund_type' => $amount ? 'partial' : 'full',
            ];

            $refundData['signature'] = $this->generateSignature($refundData);

            return [
                'success' => true,
                'refund_id' => 'corvus_refund_'.uniqid(),
                'gateway' => 'corvuspay',
                'status' => 'pending',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Refund failed: '.$e->getMessage(),
                'gateway' => 'corvuspay',
            ];
        }
    }

    public function verifyWebhook(string $payload, string $signature): bool
    {
        $data = json_decode($payload, true);

        if (! $data) {
            return false;
        }

        $expectedSignature = $this->generateSignature([
            'order_id' => $data['order_id'] ?? '',
            'status' => $data['status'] ?? '',
            'amount' => $data['amount'] ?? '',
        ]);

        return hash_equals($expectedSignature, $signature);
    }

    public function getPaymentStatus(string $transactionId): string
    {
        return 'success';
    }

    public function getName(): string
    {
        return 'CorvusPay';
    }

    public function isAvailable(): bool
    {
        return config('payment.corvuspay.enabled', true)
            && ! empty($this->storeId)
            && $this->storeId !== 'test_store_id';
    }

    /**
     * Generate CorvusPay-compatible signature
     */
    /**
     * @param array<string, int|string|null> $data
     */
    protected function generateSignature(array $data): string
    {
        $stringToSign = implode('|', array_values($data));

        return hash('sha256', $stringToSign.$this->secretKey);
    }

    /**
     * Get installment options
     */
    /**
     * @return list<array{installments: int, amount_per_installment: int, total_amount: int}>
     */
    public function getInstallmentOptions(int $amountInCents): array
    {
        $config = config('payment.corvuspay.installments', ['enabled' => false]);

        if (! $config['enabled']) {
            return [];
        }

        $options = [];
        for ($i = 2; $i <= $config['max_installments']; $i++) {
            $options[] = [
                'installments' => $i,
                'amount_per_installment' => intdiv($amountInCents, $i),
                'total_amount' => $amountInCents,
            ];
        }

        return $options;
    }
}
