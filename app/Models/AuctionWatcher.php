<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionWatcher extends Model
{
    use HasUuids;

    protected $table = 'auction_watchers';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['auction_id', 'user_id', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn ($m) => $m->created_at = now());
    }

    public function auction(): BelongsTo { return $this->belongsTo(Auction::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
}
