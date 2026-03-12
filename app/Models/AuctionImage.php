<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionImage extends Model
{
    /** @use HasFactory<Factory<self>> */
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'auction_id',
        'url',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * @return BelongsTo<Auction, $this>
     */
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeOrderBySort(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
