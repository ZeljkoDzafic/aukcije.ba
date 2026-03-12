<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bid extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Only created_at; no updated_at column.
     */
    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'auction_id',
        'user_id',
        'amount',
        'is_winning',
        'is_proxy',
        'is_auto',
        'max_proxy_amount',
        'created_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'max_proxy_amount' => 'decimal:2',
        'is_winning' => 'boolean',
        'is_proxy' => 'boolean',
        'is_auto' => 'boolean',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForAuction($query, $auctionId)
    {
        return $query->where('auction_id', $auctionId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWinning($query)
    {
        return $query->where('is_winning', true);
    }

    public function scopeOrderByAmount($query)
    {
        return $query->orderBy('amount', 'desc');
    }

    /**
     * Scope: only proxy bids.
     */
    public function scopeProxy($query)
    {
        return $query->where('is_proxy', true);
    }

    /**
     * Scope: only auto bids.
     */
    public function scopeAuto($query)
    {
        return $query->where('is_auto', true);
    }
}
