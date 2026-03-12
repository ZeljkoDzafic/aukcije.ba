<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionNotification extends Model
{
    use HasUuids;

    protected $table = 'notifications_custom';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'type', 'title', 'body', 'data', 'read_at', 'created_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn ($m) => $m->created_at = now());
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
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeForUser(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
