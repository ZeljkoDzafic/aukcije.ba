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

    public function __construct(
        StripeGateway $stripe,
        MonriGateway $monri,
        CorvusPayGateway $corvusPay,
        WalletGateway $wallet,
    ) {
        $this->gateways = [
            'stripe'     => $stripe,
            'monri'      => $monri,
            'corvuspay'  => $corvusPay,
            'wallet'     => $wallet,
        ];
    }

    public function getGateway(string $name): ?PaymentGatewayInterface
    {
        return $this->gateways[$name] ?? null;
    }

    /**
     * @return array<string, array{name: string, display_name: string, icon: string, description: string, supported_cards: list<string>}>
     */
    public function getAvailableGateways(?User $user = null, ?float $amount = null): array
    {
        $available = [];
        $enabled = config('payment.enabled', array_keys($this->gateways));

        foreach ($enabled as $gatewayName) {
            $gateway = $this->getGateway($gatewayName);

            if (! $gateway || ! $gateway->isAvailable()) {
                continue;
            }

            if ($gatewayName === 'wallet') {
                if (! $user) {
                    continue;
                }

                // Only show wallet option when user has sufficient balance for the purchase amount
                if ($amount !== null && $user->wallet && $user->wallet->available_balance < $amount) {
                    continue;
                }
            }

            $available[$gatewayName] = [
                'name'            => $gateway->getName(),
                'display_name'    => $this->getDisplayName($gatewayName),
                'icon'            => $this->getIcon($gatewayName),
                'description'     => $this->getDescription($gatewayName),
                'supported_cards' => $this->getSupportedCards($gatewayName),
            ];
        }

        return $available;
    }

    /**
     * Initiate a wallet deposit via an external payment gateway.
     * Returns a redirect URL for the user to complete payment.
     * The wallet is credited only after the webhook confirms payment.
     *
     * @return array{success: bool, redirect_url?: string, error?: string}
     */
    public function initiateDeposit(User $user, float $amount, string $gatewayName, string $returnUrl): array
    {
        $gateway = $this->getGateway($gatewayName);

        if (! $gateway || ! $gateway->isAvailable()) {
            return ['success' => false, 'error' => 'Payment gateway nije dostupan.'];
        }

        return $gateway->processPayment($amount, [
            'description'  => 'Dopuna novčanika — '.number_format($amount, 2).' BAM',
            'user_id'      => $user->id,
            'type'         => 'wallet_deposit',
            'success_url'  => $returnUrl.'?deposit=success',
            'cancel_url'   => $returnUrl.'?deposit=cancelled',
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function processPayment(Order $order, string $gatewayName, array $data): array
    {
        $gateway = $this->getGateway($gatewayName);

        if (! $gateway) {
            return ['success' => false, 'error' => 'Payment gateway not found'];
        }

        if (! $gateway->isAvailable()) {
            return ['success' => false, 'error' => 'Payment gateway is not available'];
        }

        $payment = Payment::create([
            'order_id'  => $order->id,
            'gateway'   => $gatewayName,
            'amount'    => $order->total_amount,
            'currency'  => config('payment.primary_currency', 'BAM'),
            'status'    => 'pending',
            'metadata'  => $data,
        ]);

        try {
            $result = $gateway->processPayment($order->total_amount, array_merge($data, [
                'order_id'    => $order->id,
                'description' => 'Plaćanje narudžbe #'.$order->id,
            ]));

            if ($result['success']) {
                $payment->update([
                    'transaction_id' => $result['transaction_id'],
                    'status'         => $result['status'] ?? 'pending',
                    'metadata'       => array_merge($payment->metadata, $result),
                ]);

                if (($result['status'] ?? '') === 'success') {
                    $order->markAsPaid($payment);
                }
            } else {
                $payment->update([
                    'status'        => 'failed',
                    'error_message' => $result['error'] ?? 'Unknown error',
                ]);
            }

            return $result;
        } catch (Exception $e) {
            $payment->update(['status' => 'failed', 'error_message' => $e->getMessage()]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function refund(Payment $payment, ?float $amount = null): array
    {
        if ($payment->status !== 'success') {
            return ['success' => false, 'error' => 'Can only refund successful payments'];
        }

        $gateway = $this->getGateway($payment->gateway);

        if (! $gateway) {
            return ['success' => false, 'error' => 'Payment gateway not found'];
        }

        $refundAmount = $amount ?? $payment->amount;

        if ($refundAmount > $payment->amount) {
            return ['success' => false, 'error' => 'Refund amount cannot exceed original payment'];
        }

        try {
            $result = $gateway->refund($payment->transaction_id, $refundAmount);

            if ($result['success']) {
                $payment->refunds()->create([
                    'amount'               => $refundAmount,
                    'refund_transaction_id' => $result['refund_id'],
                    'status'               => $result['status'] ?? 'pending',
                    'reason'               => $result['reason'] ?? 'Customer request',
                ]);

                if ($refundAmount >= $payment->amount) {
                    $payment->update(['status' => 'refunded']);
                }
            }

            return $result;
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function verifyWebhook(string $gatewayName, string $payload, string $signature): bool
    {
        $gateway = $this->getGateway($gatewayName);

        return $gateway ? $gateway->verifyWebhook($payload, $signature) : false;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function handleWebhook(string $gatewayName, array $data): void
    {
        $payment = Payment::where('transaction_id', $data['transaction_id'] ?? null)
            ->orWhere('metadata->order_id', $data['order_id'] ?? null)
            ->first();

        if (! $payment) {
            throw new Exception('Payment not found');
        }

        $status = $this->mapWebhookStatus($gatewayName, $data['status'] ?? '');

        if ($payment->status === $status && $payment->gateway_response === $data) {
            return;
        }

        $payment->update(['status' => $status, 'gateway_response' => $data]);

        if ($status === 'success' && $payment->order && ! $payment->order->isPaid()) {
            $payment->order->markAsPaid($payment);
        }
    }

    protected function mapWebhookStatus(string $gateway, string $status): string
    {
        return match (strtolower($status)) {
            'success', 'approved', 'completed', 'succeeded' => 'success',
            'pending', 'processing', 'waiting'               => 'pending',
            'failed', 'declined', 'rejected', 'error'        => 'failed',
            'refunded'                                        => 'refunded',
            default                                           => 'unknown',
        };
    }

    protected function getDisplayName(string $gateway): string
    {
        return match ($gateway) {
            'stripe'    => 'Kartice (Stripe)',
            'monri'     => 'BiH Kartice (Monri)',
            'corvuspay' => 'HR Kartice (CorvusPay)',
            'wallet'    => 'Interni novčanik',
            default     => $gateway,
        };
    }

    protected function getIcon(string $gateway): string
    {
        return match ($gateway) {
            'stripe'    => '💳',
            'monri'     => '🇧🇦',
            'corvuspay' => '🇭🇷',
            'wallet'    => '💰',
            default     => '💳',
        };
    }

    protected function getDescription(string $gateway): string
    {
        return match ($gateway) {
            'stripe'    => 'Visa, Mastercard, American Express',
            'monri'     => 'Lokalne BiH kartice',
            'corvuspay' => 'Hrvatske kartice',
            'wallet'    => 'Plati sa svog novčanika',
            default     => '',
        };
    }

    /**
     * @return list<string>
     */
    protected function getSupportedCards(string $gateway): array
    {
        return match ($gateway) {
            'stripe'    => ['visa', 'mastercard', 'amex', 'maestro'],
            'monri'     => ['visa', 'mastercard', 'maestro', 'amex'],
            'corvuspay' => ['visa', 'mastercard', 'maestro', 'diners'],
            'wallet'    => [],
            default     => [],
        };
    }
}
