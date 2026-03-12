<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\Gateways\CorvusPayGateway;
use App\Services\Gateways\MonriGateway;
use App\Services\Gateways\PaymentGatewayInterface;
use App\Services\Gateways\StripeGateway;
use App\Services\Gateways\WalletGateway;
use Exception;

class PaymentService
{
    /** @var array<string, PaymentGatewayInterface> */
    protected array $gateways = [];

    public function __construct()
    {
        $this->initializeGateways();
    }

    /**
     * Initialize all configured payment gateways
     */
    protected function initializeGateways(): void
    {
        $this->gateways = [
            'stripe' => new StripeGateway,
            'monri' => new MonriGateway,
            'corvuspay' => new CorvusPayGateway,
            'wallet' => new WalletGateway,
        ];
    }

    /**
     * Get a specific gateway
     */
    public function getGateway(string $name): ?PaymentGatewayInterface
    {
        return $this->gateways[$name] ?? null;
    }

    /**
     * Get all available gateways
     */
    /**
     * @return array<string, array{name: string, display_name: string, icon: string, description: string, supported_cards: list<string>}>
     */
    public function getAvailableGateways(?User $user = null): array
    {
        $available = [];
        $enabled = config('payment.enabled', array_keys($this->gateways));

        foreach ($enabled as $gatewayName) {
            $gateway = $this->getGateway($gatewayName);

            if ($gateway && $gateway->isAvailable()) {
                // Special check for wallet - only if user is authenticated
                if ($gatewayName === 'wallet' && ! $user) {
                    continue;
                }

                // Check wallet balance for wallet gateway
                if ($gatewayName === 'wallet' && $user) {
                    /** @var WalletGateway $walletGateway */
                    $walletGateway = $gateway;
                    // We don't have amount here, so just check if wallet feature is enabled
                }

                $available[$gatewayName] = [
                    'name' => $gateway->getName(),
                    'display_name' => $this->getDisplayName($gatewayName),
                    'icon' => $this->getIcon($gatewayName),
                    'description' => $this->getDescription($gatewayName),
                    'supported_cards' => $this->getSupportedCards($gatewayName),
                ];
            }
        }

        return $available;
    }

    /**
     * Process a payment
     */
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function processPayment(Order $order, string $gatewayName, array $data): array
    {
        $gateway = $this->getGateway($gatewayName);

        if (! $gateway) {
            return [
                'success' => false,
                'error' => 'Payment gateway not found',
            ];
        }

        if (! $gateway->isAvailable()) {
            return [
                'success' => false,
                'error' => 'Payment gateway is not available',
            ];
        }

        // Create payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'gateway' => $gatewayName,
            'amount' => $order->total_amount,
            'currency' => config('payment.primary_currency', 'BAM'),
            'status' => 'pending',
            'metadata' => $data,
        ]);

        try {
            $result = $gateway->processPayment($order->total_amount, array_merge($data, [
                'order_id' => $order->id,
                'description' => 'Plaćanje narudžbe #'.$order->id,
            ]));

            if ($result['success']) {
                $payment->update([
                    'transaction_id' => $result['transaction_id'],
                    'status' => $result['status'] ?? 'pending',
                    'metadata' => array_merge($payment->metadata, $result),
                ]);

                // If instant success (wallet), update order
                if (($result['status'] ?? '') === 'success') {
                    $order->markAsPaid($payment);
                }
            } else {
                $payment->update([
                    'status' => 'failed',
                    'error_message' => $result['error'] ?? 'Unknown error',
                ]);
            }

            return $result;
        } catch (Exception $e) {
            $payment->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process a refund
     */
    /**
     * @return array<string, mixed>
     */
    public function refund(Payment $payment, ?float $amount = null): array
    {
        if ($payment->status !== 'success') {
            return [
                'success' => false,
                'error' => 'Can only refund successful payments',
            ];
        }

        $gateway = $this->getGateway($payment->gateway);

        if (! $gateway) {
            return [
                'success' => false,
                'error' => 'Payment gateway not found',
            ];
        }

        $refundAmount = $amount ?? $payment->amount;

        if ($refundAmount > $payment->amount) {
            return [
                'success' => false,
                'error' => 'Refund amount cannot exceed original payment',
            ];
        }

        try {
            $result = $gateway->refund($payment->transaction_id, $refundAmount);

            if ($result['success']) {
                // Create refund record
                $payment->refunds()->create([
                    'amount' => $refundAmount,
                    'refund_transaction_id' => $result['refund_id'],
                    'status' => $result['status'] ?? 'pending',
                    'reason' => $result['reason'] ?? 'Customer request',
                ]);

                // Update payment status if full refund
                if ($refundAmount >= $payment->amount) {
                    $payment->update(['status' => 'refunded']);
                }
            }

            return $result;
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify webhook from payment gateway
     */
    public function verifyWebhook(string $gatewayName, string $payload, string $signature): bool
    {
        $gateway = $this->getGateway($gatewayName);

        if (! $gateway) {
            return false;
        }

        return $gateway->verifyWebhook($payload, $signature);
    }

    /**
     * Handle webhook event
     */
    /**
     * @param array<string, mixed> $data
     */
    public function handleWebhook(string $gatewayName, array $data): void
    {
        // Find payment by transaction ID or order ID
        $payment = Payment::where('transaction_id', $data['transaction_id'] ?? null)
            ->orWhere('metadata->order_id', $data['order_id'] ?? null)
            ->first();

        if (! $payment) {
            throw new Exception('Payment not found');
        }

        // Update payment status based on webhook data
        $status = $this->mapWebhookStatus($gatewayName, $data['status'] ?? '');

        $payment->update([
            'status' => $status,
            'gateway_response' => $data,
        ]);

        // If payment successful, update order
        if ($status === 'success') {
            $payment->order->markAsPaid($payment);
        }
    }

    /**
     * Map gateway-specific status to our status
     */
    protected function mapWebhookStatus(string $gateway, string $status): string
    {
        return match (strtolower($status)) {
            'success', 'approved', 'completed', 'succeeded' => 'success',
            'pending', 'processing', 'waiting' => 'pending',
            'failed', 'declined', 'rejected', 'error' => 'failed',
            'refunded' => 'refunded',
            default => 'unknown',
        };
    }

    /**
     * Get display name for gateway
     */
    protected function getDisplayName(string $gateway): string
    {
        return match ($gateway) {
            'stripe' => 'Kartice (Stripe)',
            'monri' => 'BiH Kartice (Monri)',
            'corvuspay' => 'HR Kartice (CorvusPay)',
            'wallet' => 'Interni novčanik',
            default => $gateway,
        };
    }

    /**
     * Get icon for gateway
     */
    protected function getIcon(string $gateway): string
    {
        return match ($gateway) {
            'stripe' => '💳',
            'monri' => '🇧🇦',
            'corvuspay' => '🇭🇷',
            'wallet' => '💰',
            default => '💳',
        };
    }

    /**
     * Get description for gateway
     */
    protected function getDescription(string $gateway): string
    {
        return match ($gateway) {
            'stripe' => 'Visa, Mastercard, American Express',
            'monri' => 'Lokalne BiH kartice',
            'corvuspay' => 'Hrvatske kartice',
            'wallet' => 'Plati sa svog novčanika',
            default => '',
        };
    }

    /**
     * Get supported cards for gateway
     */
    /**
     * @return list<string>
     */
    protected function getSupportedCards(string $gateway): array
    {
        return match ($gateway) {
            'stripe' => ['visa', 'mastercard', 'amex', 'maestro'],
            'monri' => ['visa', 'mastercard', 'maestro', 'amex'],
            'corvuspay' => ['visa', 'mastercard', 'maestro', 'diners'],
            'wallet' => [],
            default => [],
        };
    }
}
