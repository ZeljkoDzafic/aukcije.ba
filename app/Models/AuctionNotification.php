<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
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
        'data'       => 'array',
        'read_at'    => 'datetime',
        'created_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn ($m) => $m->created_at = now());
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function scopeUnread($query)    { return $query->whereNull('read_at'); }
    public function scopeForUser($query, $userId) { return $query->where('user_id', $userId); }
}
