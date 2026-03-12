<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProxyBid extends Model
{
    /** @use HasFactory<Factory<self>> */
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'auction_id',
        'user_id',
        'max_amount',
        'is_active',
    ];

    protected $casts = [
        'max_amount' => 'float',
        'is_active' => 'boolean',
    ];

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
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeForAuction(Builder $query, string $auctionId): Builder
    {
        return $query->where('auction_id', $auctionId);
    }
}
