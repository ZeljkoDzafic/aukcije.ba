<?php
namespace App\Services;

use App\Exceptions\InsufficientFundsException;
use App\Exceptions\WalletFrozenException;
use App\Models\{User, Wallet, WalletTransaction};
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function getWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'escrow_balance' => 0, 'frozen' => false]
        );
    }

    public function getBalance(User $user): float
    {
        return (float) $this->getWallet($user)->balance;
    }

    public function getBalanceSummary(User $user): array
    {
        $wallet = $this->getWallet($user);

        return [
            'available' => (float) $wallet->balance,
            'escrow'    => (float) $wallet->escrow_balance,
            'total'     => (float) $wallet->balance + (float) $wallet->escrow_balance,
            'currency'  => 'BAM',
        ];
    }

    public function getTotalBalance(User $user): float
    {
        $wallet = $this->getWallet($user);

        return (float) $wallet->balance + (float) $wallet->escrow_balance;
    }

    public function hasSufficientBalance(User $user, float $amount): bool
    {
        return $this->getBalance($user) >= $amount;
    }

    public function getTransactions(User $user)
    {
        return $this->getWallet($user)
            ->transactions()
            ->latest('created_at')
            ->get();
    }

    public function freezeWallet(User $user, string $reason): Wallet
    {
        $wallet = $this->getWallet($user);
        $wallet->update([
            'frozen' => true,
            'frozen_at' => now(),
            'frozen_reason' => $reason,
        ]);

        return $wallet->fresh();
    }

    public function unfreezeWallet(User $user): Wallet
    {
        $wallet = $this->getWallet($user);
        $wallet->update([
            'frozen' => false,
            'frozen_at' => null,
            'frozen_reason' => null,
        ]);

        return $wallet->fresh();
    }

    public function deposit(User $user, float $amount, string $gateway, ?string $referenceId = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $gateway, $referenceId) {
            $wallet = $this->getWallet($user);

            if ($wallet->frozen) {
                throw new WalletFrozenException();
            }

            $wallet->increment('balance', $amount);

            return WalletTransaction::create([
                'wallet_id'      => $wallet->id,
                'user_id'        => $user->id,
                'type'           => 'deposit',
                'amount'         => $amount,
                'balance_after'  => $wallet->fresh()->balance,
                'reference_type' => 'payment',
                'reference_id'   => $referenceId,
                'description'    => "Uplata putem {$gateway}",
            ]);
        });
    }

    public function withdraw(User $user, float $amount): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount) {
            $wallet = $this->getWallet($user);

            if ($wallet->frozen) {
                throw new WalletFrozenException();
            }

            if ($wallet->balance < $amount) {
                throw new InsufficientFundsException($amount, $wallet->balance);
            }

            $wallet->decrement('balance', $amount);

            return WalletTransaction::create([
                'wallet_id'      => $wallet->id,
                'user_id'        => $user->id,
                'type'           => 'withdrawal',
                'amount'         => -$amount,
                'balance_after'  => $wallet->fresh()->balance,
                'description'    => 'Podizanje sredstava',
            ]);
        });
    }
}
