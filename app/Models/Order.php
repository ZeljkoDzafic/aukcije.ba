<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'auction_id',
        'buyer_id',
        'seller_id',
        'status',
        'total_amount',
        'commission_amount',
        'seller_payout',
        'payment_status',
        'payment_gateway',
        'paid_at',
        'payment_deadline_at',
        'shipping_method',
        'shipping_cost',
        'shipping_address',
        'shipping_city',
        'shipping_postal_code',
        'shipping_country',
        'delivered_at',
        'completed_at',
        'cancelled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'seller_payout' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'shipping_address' => 'array',
        'paid_at' => 'datetime',
        'payment_deadline_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    public function dispute(): HasOne
    {
        return $this->hasOne(Dispute::class);
    }

    /**
     * Get the ratings associated with this order.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(UserRating::class);
    }

    /**
     * Get order status badge
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending_payment' => ['label' => 'Čeka plaćanje', 'color' => 'warning'],
            'paid' => ['label' => 'Plaćeno', 'color' => 'success'],
            'awaiting_shipment' => ['label' => 'Čeka slanje', 'color' => 'info'],
            'shipped' => ['label' => 'Poslano', 'color' => 'info'],
            'delivered' => ['label' => 'Dostavljeno', 'color' => 'success'],
            'completed' => ['label' => 'Završeno', 'color' => 'success'],
            'cancelled' => ['label' => 'Otkazano', 'color' => 'danger'],
            'disputed' => ['label' => 'Spor', 'color' => 'danger'],
            default => ['label' => $this->status, 'color' => 'gray'],
        };
    }

    /**
     * Check if order needs payment
     */
    public function needsPayment(): bool
    {
        return $this->status === 'pending_payment';
    }

    /**
     * Check if order is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid' || $this->status === 'paid';
    }

    /**
     * Check if order is shipped
     */
    public function isShipped(): bool
    {
        return $this->status === 'shipped';
    }

    /**
     * Check if order is delivered
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered' || $this->status === 'completed';
    }

    /**
     * Check if order is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if order is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Mark order as paid
     */
    public function markAsPaid(Payment $payment): void
    {
        $this->update([
            'status' => 'awaiting_shipment',
            'payment_status' => 'paid',
            'payment_gateway' => $payment->gateway,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark order as shipped
     */
    public function markAsShipped(): void
    {
        $this->update([
            'status' => 'shipped',
        ]);
    }

    /**
     * Mark order as delivered
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * Mark order as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel(string $reason): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancel_reason' => $reason,
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Check if payment is overdue
     */
    public function isPaymentOverdue(): bool
    {
        return $this->needsPayment() && $this->payment_deadline_at && $this->payment_deadline_at->isPast();
    }

    /**
     * Get days until payment deadline
     */
    public function getDaysUntilPaymentDeadlineAttribute(): ?int
    {
        if (!$this->payment_deadline_at) {
            return null;
        }

        return max(0, now()->diffInDays($this->payment_deadline_at, false));
    }

    /**
     * Get days since payment
     */
    public function getDaysSincePaymentAttribute(): ?int
    {
        if (!$this->paid_at) {
            return null;
        }

        return max(0, now()->diffInDays($this->paid_at));
    }
}
