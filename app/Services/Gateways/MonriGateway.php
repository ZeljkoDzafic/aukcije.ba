<?php

declare(strict_types=1);

namespace App\Services\Gateways;

use Exception;
use Illuminate\Support\Facades\Http;

class MonriGateway implements PaymentGatewayInterface
{
    protected string $key;

    protected string $authenticityToken;

    protected string $webhookSecret;

    protected string $currency;

    protected string $apiUrl;

    public function __construct()
    {
        $this->key = config('payment.monri.key');
        $this->authenticityToken = config('payment.monri.authenticity_token');
        $this->webhookSecret = config('payment.monri.webhook_secret');
        $this->currency = config('payment.monri.currency', 'BAM');
        $this->apiUrl = config('payment.monri.sandbox', true)
            ? config('payment.monri.api_url')
            : config('payment.monri.api_url_production');
    }

    public function processPayment(float $amount, array $data): array
    {
        try {
            $orderId = $data['order_id'] ?? uniqid('ORD_');
            $amountInCents = (int) ($amount * 100);

            // Generate signature (Monri signature algorithm)
            $signatureData = [
                'amount' => $amountInCents,
                'order_info' => $data['description'] ?? 'Aukcije.ba Payment',
                'order_id' => $orderId,
                'merchant_code' => $this->key,
            ];

            $signature = $this->generateSignature($signatureData);

            // Prepare payment request
            $paymentData = [
                'amount' => $amountInCents,
                'order_id' => $orderId,
                'order_info' => $data['description'] ?? 'Aukcije.ba Payment',
                'merchant_code' => $this->key,
                'authenticity_token' => $this->authenticityToken,
                'indicator_language' => $this->getLanguageCode($data['locale'] ?? 'bs'),
                'success_url' => $data['success_url'] ?? route('payment.success'),
                'cancel_url' => $data['cancel_url'] ?? route('payment.cancel'),
                'signature' => $signature,
            ];

            // In production, send request to Monri API
            // $response = Http::post($this->apiUrl . '/payment', $paymentData);

            // Generate form for redirect (Monri uses form POST)
            $formAction = $this->apiUrl.'/payment';
            $formFields = $paymentData;

            return [
                'success' => true,
                'transaction_id' => 'monri_'.$orderId,
                'gateway' => 'monri',
                'status' => 'pending',
                'redirect_url' => $formAction,
                'form_fields' => $formFields,
                'method' => 'POST',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Payment failed: '.$e->getMessage(),
                'gateway' => 'monri',
            ];
        }
    }

    public function refund(string $transactionId, ?float $amount = null): array
    {
        try {
            // Monri refund API
            $refundData = [
                'original_order_id' => str_replace('monri_', '', $transactionId),
                'amount' => $amount ? (int) ($amount * 100) : null,
                'merchant_code' => $this->key,
                'authenticity_token' => $this->authenticityToken,
            ];

            $refundData['signature'] = $this->generateSignature($refundData);

            // In production:
            // $response = Http::post($this->apiUrl . '/refund', $refundData);

            return [
                'success' => true,
                'refund_id' => 'monri_refund_'.uniqid(),
                'gateway' => 'monri',
                'status' => 'pending',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Refund failed: '.$e->getMessage(),
                'gateway' => 'monri',
            ];
        }
    }

    public function verifyWebhook(string $payload, string $signature): bool
    {
        // Verify Monri webhook signature
        $data = json_decode($payload, true);

        if (! $data) {
            return false;
        }

        $expectedSignature = $this->generateSignature([
            'order_id' => $data['order_id'] ?? '',
            'status' => $data['status'] ?? '',
            'response_code' => $data['response_code'] ?? '',
        ]);

        return hash_equals($expectedSignature, $signature);
    }

    public function getPaymentStatus(string $transactionId): string
    {
        // In production, fetch from Monri API
        return 'success';
    }

    public function getName(): string
    {
        return 'Monri';
    }

    public function isAvailable(): bool
    {
        return config('payment.monri.enabled', true)
            && ! empty($this->key)
            && $this->key !== 'test_monri_key';
    }

    /**
     * Generate Monri-compatible signature
     */
    /**
     * @param array<string, int|string|null> $data
     */
    protected function generateSignature(array $data): string
    {
        $stringToSign = implode('', $data);

        return hash('sha512', $stringToSign.$this->authenticityToken);
    }

    /**
     * Get language code for Monri
     */
    protected function getLanguageCode(string $locale): int
    {
        return match ($locale) {
            'bs', 'sr', 'hr' => 1, // Bosnian/Croatian/Serbian
            'de' => 2, // German
            'it' => 3, // Italian
            'en' => 4, // English
            default => 1,
        };
    }

    /**
     * Get supported card types
     */
    /**
     * @return list<string>
     */
    public function getSupportedCardTypes(): array
    {
        return config('payment.monri.card_types', ['visa', 'mastercard', 'maestro', 'amex']);
    }
}
