<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $status
 */
class PaymentRefund extends Model
{
    /** @use HasFactory<Factory<self>> */
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'amount',
        'refund_transaction_id',
        'status',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * @return BelongsTo<Payment, $this>
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
