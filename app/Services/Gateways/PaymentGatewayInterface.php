<?php

namespace App\Services\Gateways;

interface PaymentGatewayInterface
{
    /**
     * Process a payment
     *
     * @param  float  $amount
     * @param  array  $data  Payment data (card info, return URLs, etc.)
     * @return array Payment result [success, transaction_id, redirect_url?, error?]
     */
    public function processPayment(float $amount, array $data): array;

    /**
     * Refund a payment
     *
     * @param  string  $transactionId
     * @param  float|null  $amount  Null for full refund
     * @return array Refund result [success, refund_id, error?]
     */
    public function refund(string $transactionId, ?float $amount = null): array;

    /**
     * Verify webhook signature
     *
     * @param  string  $payload
     * @param  string  $signature
     * @return bool
     */
    public function verifyWebhook(string $payload, string $signature): bool;

    /**
     * Get payment status
     *
     * @param  string  $transactionId
     * @return string Status: pending, success, failed, refunded
     */
    public function getPaymentStatus(string $transactionId): string;

    /**
     * Get gateway name
     */
    public function getName(): string;

    /**
     * Check if gateway is available
     */
    public function isAvailable(): bool;
}
