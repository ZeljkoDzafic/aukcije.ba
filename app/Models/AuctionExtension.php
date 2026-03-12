<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $auction_id
 * @property string|null $triggered_by_bid_id
 */
class AuctionExtension extends Model
{
    /** @use HasFactory<Factory<self>> */
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Only created_at is managed; no updated_at column.
     */
    public $timestamps = false;

    /**
     * Manually handle created_at.
     */
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'auction_id',
        'triggered_by_bid_id',
        'old_end_at',
        'new_end_at',
        'created_at',
    ];

    protected $casts = [
        'old_end_at' => 'datetime',
        'new_end_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Boot: set created_at on create.
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }

    /**
     * Get the auction this extension belongs to.
     */
    /**
     * @return BelongsTo<Auction, $this>
     */
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    /**
     * Get the bid that triggered this extension.
     */
    /**
     * @return BelongsTo<Bid, $this>
     */
    public function triggeringBid(): BelongsTo
    {
        return $this->belongsTo(Bid::class, 'triggered_by_bid_id');
    }

    /**
     * Alias kept for backwards compatibility.
     */
    /**
     * @return BelongsTo<Bid, $this>
     */
    public function bid(): BelongsTo
    {
        return $this->triggeringBid();
    }
}
