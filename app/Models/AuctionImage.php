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
        'blurhash',
        'optimized_urls',
        'width',
        'height',
    ];

    protected $casts = [
        'is_primary'     => 'boolean',
        'optimized_urls' => 'array',
        'width'          => 'integer',
        'height'         => 'integer',
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

    /**
     * Return the URL for a given size preset (thumbnail|medium|large|original).
     * Falls back to the canonical URL if optimized variant is unavailable.
     */
    public function getOptimizedUrl(string $preset = 'medium'): string
    {
        $urls = $this->optimized_urls;

        if (is_array($urls) && isset($urls[$preset]) && $urls[$preset] !== '') {
            return (string) $urls[$preset];
        }

        return $this->url;
    }

    /**
     * Build an HTML srcset string from optimized variants.
     *
     * Example output:
     *   https://cdn.example.com/img-thumbnail.webp 400w, https://cdn.example.com/img-medium.webp 800w, ...
     */
    public function getSrcset(): string
    {
        $urls = $this->optimized_urls;

        if (! is_array($urls)) {
            return $this->url;
        }

        $widths = ['thumbnail' => 400, 'medium' => 800, 'large' => 1600];
        $parts  = [];

        foreach ($widths as $size => $width) {
            if (! empty($urls[$size])) {
                $parts[] = "{$urls[$size]} {$width}w";
            }
        }

        return $parts !== [] ? implode(', ', $parts) : $this->url;
    }
}
