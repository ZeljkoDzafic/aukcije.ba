<?php

declare(strict_types=1);

namespace App\Services\Gateways;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Exception;

class WalletGateway implements PaymentGatewayInterface
{
    /**
     * Process payment from user's wallet
     */
    public function processPayment(float $amount, array $data): array
    {
        try {
            $user = $data['user'] ?? null;

            if (! $user) {
                return [
                    'success' => false,
                    'error' => 'User not provided',
                    'gateway' => 'wallet',
                ];
            }

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0]
            );

            // Check sufficient balance
            if ($wallet->balance < $amount) {
                return [
                    'success' => false,
                    'error' => 'Nedovoljno sredstava na novčaniku',
                    'gateway' => 'wallet',
                    'current_balance' => $wallet->balance,
                    'required' => $amount,
                ];
            }

            // Deduct amount
            $wallet->decrement('balance', $amount);

            // Create transaction record
            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => 'payment',
                'amount' => -$amount,
                'balance_after' => $wallet->balance,
                'description' => $data['description'] ?? 'Plaćanje narudžbe',
                'reference_type' => $data['reference_type'] ?? 'order',
                'reference_id' => $data['order_id'] ?? null,
            ]);

            return [
                'success' => true,
                'transaction_id' => 'wallet_'.$transaction->id,
                'gateway' => 'wallet',
                'status' => 'success',
                'balance_remaining' => $wallet->balance,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Payment failed: '.$e->getMessage(),
                'gateway' => 'wallet',
            ];
        }
    }

    /**
     * Refund to user's wallet
     */
    public function refund(string $transactionId, ?float $amount = null): array
    {
        try {
            $originalTransaction = WalletTransaction::find(str_replace('wallet_', '', $transactionId));

            if (! $originalTransaction) {
                return [
                    'success' => false,
                    'error' => 'Original transaction not found',
                    'gateway' => 'wallet',
                ];
            }

            $refundAmount = $amount ?? abs($originalTransaction->amount);
            $user = User::find($originalTransaction->user_id);
            $wallet = $user->wallet;

            // Add refund amount
            $wallet->increment('balance', $refundAmount);

            // Create refund transaction
            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => 'refund',
                'amount' => $refundAmount,
                'balance_after' => $wallet->balance,
                'description' => 'Refund for transaction #'.$transactionId,
                'reference_type' => 'refund',
                'reference_id' => $originalTransaction->id,
            ]);

            return [
                'success' => true,
                'refund_id' => 'wallet_refund_'.$transaction->id,
                'gateway' => 'wallet',
                'status' => 'success',
                'new_balance' => $wallet->balance,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Refund failed: '.$e->getMessage(),
                'gateway' => 'wallet',
            ];
        }
    }

    public function verifyWebhook(string $payload, string $signature): bool
    {
        // Wallet doesn't use webhooks
        return true;
    }

    public function getPaymentStatus(string $transactionId): string
    {
        $transaction = WalletTransaction::find(str_replace('wallet_', '', $transactionId));

        if (! $transaction) {
            return 'unknown';
        }

        return 'success';
    }

    public function getName(): string
    {
        return 'Interni novčanik';
    }

    public function isAvailable(): bool
    {
        return config('payment.wallet.enabled', true);
    }

    /**
     * Get wallet balance for user
     */
    public function getBalance(User $user): float
    {
        $wallet = Wallet::where('user_id', $user->id)->first();

        return $wallet?->balance ?? 0;
    }

    /**
     * Check if user has sufficient balance
     */
    public function hasSufficientBalance(User $user, float $amount): bool
    {
        return $this->getBalance($user) >= $amount;
    }

    /**
     * Get payment limits
     */
    /**
     * @return array{min_payment: int, max_payment: int, daily_limit: int}
     */
    public function getLimits(): array
    {
        return config('payment.wallet.limits', [
            'min_payment' => 1,
            'max_payment' => 5000,
            'daily_limit' => 10000,
        ]);
    }
}
