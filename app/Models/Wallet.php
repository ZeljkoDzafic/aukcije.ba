<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    /** @use HasFactory<Factory<self>> */
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'balance',
        'escrow_balance',
        'frozen',
        'frozen_at',
        'frozen_reason',
    ];

    protected $casts = [
        'balance' => 'float',
        'escrow_balance' => 'float',
        'frozen' => 'boolean',
        'frozen_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<WalletTransaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Available balance = current balance (escrow already deducted separately).
     */
    public function getAvailableBalanceAttribute(): float
    {
        return (float) $this->balance;
    }

    public function getTotalBalanceAttribute(): float
    {
        return $this->balance + $this->escrow_balance;
    }

    public function isFrozen(): bool
    {
        return $this->frozen;
    }

    public function canWithdraw(): bool
    {
        return ! $this->frozen && $this->balance > 0;
    }

    public function canPay(float $amount): bool
    {
        return ! $this->frozen && $this->balance >= $amount;
    }

    /**
     * Add funds to wallet
     */
    public function deposit(float $amount, string $description = '', ?string $reference = null): WalletTransaction
    {
        $this->increment('balance', $amount);

        return WalletTransaction::create([
            'wallet_id' => $this->id,
            'user_id' => $this->user_id,
            'type' => 'deposit',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'description' => $description,
            'reference_type' => 'deposit',
            'reference_id' => $reference,
        ]);
    }

    /**
     * Withdraw funds from wallet
     */
    public function withdraw(float $amount, string $description = '', ?string $reference = null): WalletTransaction
    {
        if (! $this->canPay($amount)) {
            throw new \Exception('Insufficient balance');
        }

        $this->decrement('balance', $amount);

        return WalletTransaction::create([
            'wallet_id' => $this->id,
            'user_id' => $this->user_id,
            'type' => 'withdrawal',
            'amount' => -$amount,
            'balance_after' => $this->balance,
            'description' => $description,
            'reference_type' => 'withdrawal',
            'reference_id' => $reference,
        ]);
    }

    /**
     * Hold funds in escrow
     */
    public function holdForEscrow(float $amount): WalletTransaction
    {
        if (! $this->canPay($amount)) {
            throw new \Exception('Insufficient balance');
        }

        $this->decrement('balance', $amount);
        $this->increment('escrow_balance', $amount);

        return WalletTransaction::create([
            'wallet_id' => $this->id,
            'user_id' => $this->user_id,
            'type' => 'escrow_hold',
            'amount' => -$amount,
            'balance_after' => $this->balance,
            'description' => 'Sredstva zamrznuta u escrow',
        ]);
    }

    /**
     * Release funds from escrow
     */
    public function releaseFromEscrow(float $amount): WalletTransaction
    {
        $this->decrement('escrow_balance', $amount);

        return WalletTransaction::create([
            'wallet_id' => $this->id,
            'user_id' => $this->user_id,
            'type' => 'escrow_release',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'description' => 'Sredstva oslobođena iz escrow',
        ]);
    }
}
