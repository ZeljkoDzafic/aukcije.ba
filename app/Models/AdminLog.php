<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $admin_id
 * @property array<string, mixed>|null $metadata
 */
class AdminLog extends Model
{
    use HasUuids;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'admin_id', 'action', 'target_type', 'target_id', 'metadata', 'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
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
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
