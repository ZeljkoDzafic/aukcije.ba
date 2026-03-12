<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRating extends Model
{
    /** @use HasFactory<Factory<self>> */
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    public const CREATED_AT = 'created_at';

    protected $fillable = [
        'order_id',
        'rater_id',
        'rated_id',
        'score',
        'comment',
        'is_visible',
    ];

    protected $casts = [
        'score' => 'integer',
        'is_visible' => 'boolean',
    ];

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function rated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_id');
    }

    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    /**
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeForUser(Builder $query, string $userId): Builder
    {
        return $query->where('rated_id', $userId);
    }
}
