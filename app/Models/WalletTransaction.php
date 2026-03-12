<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    /** @use HasFactory<Factory<self>> */
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Only created_at; no updated_at.
     */
    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }

    protected $fillable = [
        'wallet_id',
        'user_id',
        'type',
        'amount',
        'balance_after',
        'description',
        'reference_type',
        'reference_id',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'float',
        'balance_after' => 'float',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Wallet, $this>
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for deposits
     */
    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeDeposits(Builder $query): Builder
    {
        return $query->where('type', 'deposit');
    }

    /**
     * Scope for withdrawals
     */
    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeWithdrawals(Builder $query): Builder
    {
        return $query->where('type', 'withdrawal');
    }

    /**
     * Scope for payments
     */
    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopePayments(Builder $query): Builder
    {
        return $query->where('type', 'payment');
    }

    /**
     * Scope for refunds
     */
    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeRefunds(Builder $query): Builder
    {
        return $query->where('type', 'refund');
    }

    /**
     * Scope for escrow holds
     */
    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeEscrowHolds(Builder $query): Builder
    {
        return $query->where('type', 'escrow_hold');
    }

    /**
     * Scope for escrow releases
     */
    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeEscrowReleases(Builder $query): Builder
    {
        return $query->where('type', 'escrow_release');
    }

    /**
     * Get transaction type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'deposit' => 'Uplata',
            'withdrawal' => 'Isplata',
            'payment' => 'Plaćanje',
            'refund' => 'Refund',
            'escrow_hold' => 'Escrow zamrzavanje',
            'escrow_release' => 'Escrow oslobađanje',
            'commission' => 'Provizija',
            'adjustment' => 'Korekcija',
            default => $this->type,
        };
    }

    /**
     * Get transaction type icon
     */
    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'deposit' => '⬆️',
            'withdrawal' => '⬇️',
            'payment' => '💳',
            'refund' => '↩️',
            'escrow_hold' => '🔒',
            'escrow_release' => '🔓',
            'commission' => '💰',
            'adjustment' => '⚙️',
            default => '📝',
        };
    }

    /**
     * Check if transaction is positive (money in)
     */
    public function isPositive(): bool
    {
        return $this->amount > 0;
    }

    /**
     * Check if transaction is negative (money out)
     */
    public function isNegative(): bool
    {
        return $this->amount < 0;
    }
}
