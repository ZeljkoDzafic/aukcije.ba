<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AuctionWatcher extends Pivot
{
    use HasUuids;

    protected $table = 'auction_watchers';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = true;

    protected $fillable = ['id', 'auction_id', 'user_id', 'created_at', 'updated_at'];

    protected $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $watcher): void {
            $watcher->created_at ??= now();
            $watcher->updated_at ??= now();
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
}
