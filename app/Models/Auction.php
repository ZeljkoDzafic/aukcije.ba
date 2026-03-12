<?php

namespace App\Models;

use App\Enums\AuctionStatus;
use App\Enums\AuctionType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Auction extends Model
{
    use HasFactory, HasUuids, SoftDeletes, Searchable;

    /**
     * The primary key.
     */
    protected $primaryKey = 'id';

    /**
     * UUID primary key type.
     */
    protected $keyType = 'string';

    /**
     * UUIDs are not auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'seller_id',
        'category_id',
        'title',
        'description',
        'start_price',
        'current_price',
        'buy_now_price',
        'reserve_price',
        'type',
        'condition',
        'status',
        'duration_days',
        'starts_at',
        'ends_at',
        'original_end_at',
        'started_at',
        'ended_at',
        'auto_extension',
        'extension_minutes',
        'bids_count',
        'views_count',
        'watchers_count',
        'winner_id',
        'last_bid_at',
        'is_featured',
        'featured_until',
        'shipping_available',
        'shipping_cost',
        'shipping_info',
        'location',
        'slug',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start_price' => 'decimal:2',
        'current_price' => 'decimal:2',
        'buy_now_price' => 'decimal:2',
        'reserve_price' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'original_end_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'last_bid_at' => 'datetime',
        'featured_until' => 'datetime',
        'auto_extension' => 'boolean',
        'is_featured' => 'boolean',
        'shipping_available' => 'boolean',
        'bids_count' => 'integer',
        'views_count' => 'integer',
        'watchers_count' => 'integer',
        'type' => AuctionType::class,
        'status' => AuctionStatus::class,
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    /**
     * Get the seller (User) who created the auction.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the category this auction belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the auction winner.
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    /**
     * Get all bids placed on this auction.
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    /**
     * Get all proxy bids for this auction.
     */
    public function proxyBids(): HasMany
    {
        return $this->hasMany(ProxyBid::class);
    }

    /**
     * Get all images for this auction.
     */
    public function images(): HasMany
    {
        return $this->hasMany(AuctionImage::class);
    }

    /**
     * Get all user watchers for this auction (pivot).
     */
    public function watchers(): HasMany
    {
        return $this->hasMany(AuctionWatcher::class);
    }

    /**
     * Get users watching this auction (many-to-many via pivot).
     */
    public function watchingUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'auction_watchers')->withTimestamps();
    }

    /**
     * Get all time extensions for this auction.
     */
    public function extensions(): HasMany
    {
        return $this->hasMany(AuctionExtension::class);
    }

    /**
     * Get the order associated with this auction (after it closes).
     */
    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }

    /**
     * Get the primary (first) image for this auction.
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(AuctionImage::class)->ofMany('sort_order', 'min');
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Get the human-readable time remaining until the auction ends.
     */
    public function getTimeRemainingAttribute(): string
    {
        $now = now();

        if (!$this->ends_at || $this->ends_at <= $now) {
            return 'Završeno';
        }

        $diff = $this->ends_at->diff($now);

        if ($diff->d > 0) {
            return "{$diff->d}d {$diff->h}h";
        }

        if ($diff->h > 0) {
            return "{$diff->h}h {$diff->i}m";
        }

        return "{$diff->i}m {$diff->s}s";
    }

    /**
     * Get the minimum acceptable next bid amount.
     */
    public function getMinimumBidAttribute(): float
    {
        return app(\App\Services\BidIncrementService::class)->getMinimumBid($this->current_price);
    }

    /**
     * Determine whether the auction ends within 60 minutes.
     */
    public function getIsEndingSoonAttribute(): bool
    {
        if (!$this->ends_at || $this->ends_at->isPast()) {
            return false;
        }

        return Carbon::now()->diffInMinutes($this->ends_at, false) < 60;
    }

    /**
     * Get the winning bid for this auction.
     */
    public function getWinningBidAttribute(): ?Bid
    {
        return $this->bids()->where('is_winning', true)->first();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Scope: only active auctions.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', AuctionStatus::Active)
            ->where('ends_at', '>', now());
    }

    /**
     * Scope: auctions ending within a given number of hours (default 24).
     */
    public function scopeEndingSoon(Builder $query, int $hours = 24): Builder
    {
        return $query->active()
            ->where('ends_at', '<=', now()->addHours($hours));
    }

    /**
     * Scope: only featured auctions.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)
            ->where('featured_until', '>', now());
    }

    /**
     * Scope: auctions in a specific category.
     */
    public function scopeInCategory(Builder $query, string $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope: auctions that have at least one bid.
     */
    public function scopeWithBids(Builder $query): Builder
    {
        return $query->where('bids_count', '>', 0);
    }

    /**
     * Scope: full-text search by title / description.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    // -------------------------------------------------------------------------
    // Status helpers
    // -------------------------------------------------------------------------

    public function isActive(): bool
    {
        return $this->status === AuctionStatus::Active && $this->ends_at > now();
    }

    public function isFinished(): bool
    {
        return $this->status === AuctionStatus::Finished
            || ($this->status === AuctionStatus::Active && $this->ends_at <= now());
    }

    public function isSold(): bool
    {
        return $this->status === AuctionStatus::Sold;
    }

    public function isCancelled(): bool
    {
        return $this->status === AuctionStatus::Cancelled;
    }

    public function isDraft(): bool
    {
        return $this->status === AuctionStatus::Draft;
    }

    /**
     * Check if a given user is watching this auction.
     */
    public function isWatchedBy(User $user): bool
    {
        return $this->watchingUsers()->where('user_id', $user->id)->exists();
    }

    // -------------------------------------------------------------------------
    // Scout
    // -------------------------------------------------------------------------

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status instanceof AuctionStatus ? $this->status->value : $this->status,
            'current_price' => $this->current_price,
            'ends_at' => $this->ends_at?->toIso8601String(),
            'category_id' => $this->category_id,
            'seller_id' => $this->seller_id,
            'is_featured' => $this->is_featured,
        ];
    }
}
