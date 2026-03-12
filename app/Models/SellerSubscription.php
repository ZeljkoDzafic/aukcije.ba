<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerSubscription extends Model
{
    use HasUuids;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'tier', 'starts_at', 'ends_at', 'is_trial',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'is_trial'  => 'boolean',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function getIsActiveAttribute(): bool
    {
        return $this->ends_at === null || $this->ends_at->isFuture();
    }

    public function getTierConfigAttribute(): array
    {
        return config("tiers.tiers.{$this->tier}", config('tiers.tiers.free'));
    }

    public function getAuctionLimitAttribute(): int
    {
        return $this->tier_config['auction_limit'] ?? 5;
    }

    public function getCommissionRateAttribute(): float
    {
        return (float) ($this->tier_config['commission_rate'] ?? 8);
    }
}
