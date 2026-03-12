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

    public function getBalance(User $user): array
    {
        $wallet = $this->getWallet($user);
        return [
            'available' => $wallet->balance,
            'escrow'    => $wallet->escrow_balance,
            'total'     => $wallet->balance + $wallet->escrow_balance,
            'currency'  => 'BAM',
        ];
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
