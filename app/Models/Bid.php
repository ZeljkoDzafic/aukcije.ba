<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bid extends Model
{
    /** @use HasFactory<Factory<self>> */
    use HasFactory;
    use HasUuids;

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
        'amount' => 'float',
        'max_proxy_amount' => 'float',
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

    /**
     * @return BelongsTo<Auction, $this>
     */
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeForAuction(Builder $query, string $auctionId): Builder
    {
        return $query->where('auction_id', $auctionId);
    }

    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeByUser(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeWinning(Builder $query): Builder
    {
        return $query->where('is_winning', true);
    }

    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeOrderByAmount(Builder $query): Builder
    {
        return $query->orderBy('amount', 'desc');
    }

    /**
     * Scope: only proxy bids.
     */
    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeProxy(Builder $query): Builder
    {
        return $query->where('is_proxy', true);
    }

    /**
     * Scope: only auto bids.
     */
    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeAuto(Builder $query): Builder
    {
        return $query->where('is_auto', true);
    }

    public function getIsWinningAttribute(bool|int|string|null $value): bool
    {
        if (! $this->exists || ! $this->id) {
            return (bool) $value;
        }

        return (bool) static::query()->whereKey($this->id)->value('is_winning');
    }
}
