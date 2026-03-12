<?php

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
        try {
            // In production, use Stripe SDK:
            // \Stripe\Stripe::setApiKey($this->apiKey);
            
            // $session = \Stripe\Checkout\Session::create([
            //     'payment_method_types' => ['card'],
            //     'line_items' => [[
            //         'price_data' => [
            //             'currency' => $this->currency,
            //             'product_data' => ['name' => $data['description'] ?? 'Aukcije.ba Payment'],
            //             'unit_amount' => (int) ($amount * 100),
            //         ],
            //         'quantity' => 1,
            //     ]],
            //     'mode' => 'payment',
            //     'success_url' => $data['success_url'],
            //     'cancel_url' => $data['cancel_url'],
            //     'client_reference_id' => $data['order_id'] ?? null,
            // ]);

            // For now, return mock response
            return [
                'success' => true,
                'transaction_id' => 'stripe_' . uniqid(),
                'gateway' => 'stripe',
                'status' => 'pending',
                'redirect_url' => 'https://checkout.stripe.com/c/pay/cs_test_' . uniqid(),
                'client_secret' => 'pi_' . uniqid() . '_secret_' . uniqid(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Payment failed: ' . $e->getMessage(),
                'gateway' => 'stripe',
            ];
        }
    }

    public function refund(string $transactionId, ?float $amount = null): array
    {
        try {
            // In production, use Stripe SDK:
            // $refund = \Stripe\Refund::create([
            //     'payment_intent' => $transactionId,
            //     'amount' => $amount ? (int) ($amount * 100) : null,
            // ]);

            return [
                'success' => true,
                'refund_id' => 're_' . uniqid(),
                'gateway' => 'stripe',
                'status' => 'pending',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Refund failed: ' . $e->getMessage(),
                'gateway' => 'stripe',
            ];
        }
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
        return !empty($signature);
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
            && !empty($this->apiKey)
            && $this->apiKey !== 'sk_test_YourStripeTestKey';
    }

    /**
     * Get supported payment methods
     */
    public function getPaymentMethods(): array
    {
        return config('payment.stripe.payment_methods', ['card']);
    }

    /**
     * Get Stripe checkout configuration
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
