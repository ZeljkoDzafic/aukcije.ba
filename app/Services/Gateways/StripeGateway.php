<?php

declare(strict_types=1);

namespace App\Services\Gateways;

use Exception;

class StripeGateway implements PaymentGatewayInterface
{
    protected string $apiKey;

    protected string $webhookSecret;

    protected string $currency;

    public function __construct()
    {
        $this->apiKey = config('payment.stripe.secret');
        $this->webhookSecret = config('payment.stripe.webhook_secret');
        $this->currency = strtolower(config('payment.stripe.currency', 'EUR'));
    }

    public function processPayment(float $amount, array $data): array
    {
        return [
            'success' => true,
            'transaction_id' => 'stripe_'.uniqid(),
            'gateway' => 'stripe',
            'status' => 'pending',
            'redirect_url' => 'https://checkout.stripe.com/c/pay/cs_test_'.uniqid(),
            'client_secret' => 'pi_'.uniqid().'_secret_'.uniqid(),
        ];
    }

    public function refund(string $transactionId, ?float $amount = null): array
    {
        return [
            'success' => true,
            'refund_id' => 're_'.uniqid(),
            'gateway' => 'stripe',
            'status' => 'pending',
        ];
    }

    public function verifyWebhook(string $payload, string $signature): bool
    {
        // In production, use Stripe SDK:
        // try {
        //     \Stripe\Webhook::constructEvent(
        //         $payload,
        //         $signature,
        //         $this->webhookSecret
        //     );
        //     return true;
        // } catch (\UnexpectedValueException $e) {
        //     return false;
        // }

        // Mock verification
        return ! empty($signature);
    }

    public function getPaymentStatus(string $transactionId): string
    {
        // In production, fetch from Stripe API
        return 'success';
    }

    public function getName(): string
    {
        return 'Stripe';
    }

    public function isAvailable(): bool
    {
        return config('payment.stripe.enabled', true)
            && ! empty($this->apiKey)
            && $this->apiKey !== 'sk_test_YourStripeTestKey';
    }

    /**
     * Get supported payment methods
     */
    /**
     * @return list<string>
     */
    public function getPaymentMethods(): array
    {
        return config('payment.stripe.payment_methods', ['card']);
    }

    /**
     * Get Stripe checkout configuration
     */
    /**
     * @param array<string, mixed> $orderData
     * @return array<string, mixed>
     */
    public function getCheckoutConfig(array $orderData): array
    {
        return [
            'key' => config('payment.stripe.key'),
            'currency' => $this->currency,
            'amount' => (int) ($orderData['amount'] * 100),
            'description' => $orderData['description'] ?? 'Aukcije.ba Payment',
            'success_url' => $orderData['success_url'],
            'cancel_url' => $orderData['cancel_url'],
        ];
    }
}
