<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory, HasUuids;

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

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for deposits
     */
    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    /**
     * Scope for withdrawals
     */
    public function scopeWithdrawals($query)
    {
        return $query->where('type', 'withdrawal');
    }

    /**
     * Scope for payments
     */
    public function scopePayments($query)
    {
        return $query->where('type', 'payment');
    }

    /**
     * Scope for refunds
     */
    public function scopeRefunds($query)
    {
        return $query->where('type', 'refund');
    }

    /**
     * Scope for escrow holds
     */
    public function scopeEscrowHolds($query)
    {
        return $query->where('type', 'escrow_hold');
    }

    /**
     * Scope for escrow releases
     */
    public function scopeEscrowReleases($query)
    {
        return $query->where('type', 'escrow_release');
    }

    /**
     * Get transaction type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
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
        return match($this->type) {
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
