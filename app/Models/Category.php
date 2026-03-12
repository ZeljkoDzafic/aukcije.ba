<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string|null $parent_id
 * @property bool $is_active
 * @property int|null $sort_order
 * @property int|null $auctions_count
 *
 * @method static Builder<self> query()
 */
class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsTo<Category, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * @return HasMany<Category, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * @return HasMany<Auction, $this>
     */
    public function auctions(): HasMany
    {
        return $this->hasMany(Auction::class);
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
    public function scopeParent(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
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
