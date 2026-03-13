<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AuctionStatus;
use App\Models\Auction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * T-1202: Homepage data service with 5-minute cache per section.
 *
 * Returns 4 curated auction sections for the landing page.
 */
class HomepageDataService
{
    private const TTL = 300; // 5 minutes

    /** @return Collection<int, Auction> */
    public function featured(): Collection
    {
        return Cache::remember('homepage:featured', self::TTL, function () {
            return Auction::query()
                ->where('status', AuctionStatus::Active->value)
                ->where('is_featured', true)
                ->with(['seller:id,name', 'category', 'primaryImage', 'images'])
                ->withCount(['bids', 'watchers'])
                ->orderBy('ends_at')
                ->limit(12)
                ->get();
        });
    }

    /** @return Collection<int, Auction> */
    public function endingSoon(): Collection
    {
        return Cache::remember('homepage:ending_soon', self::TTL, function () {
            return Auction::query()
                ->where('status', AuctionStatus::Active->value)
                ->where('ends_at', '<=', now()->addHours(2))
                ->with(['seller:id,name', 'category', 'primaryImage', 'images'])
                ->withCount(['bids', 'watchers'])
                ->orderBy('ends_at')
                ->limit(12)
                ->get();
        });
    }

    /** @return Collection<int, Auction> */
    public function newArrivals(): Collection
    {
        return Cache::remember('homepage:new_arrivals', self::TTL, function () {
            return Auction::query()
                ->where('status', AuctionStatus::Active->value)
                ->where('created_at', '>=', now()->subDay())
                ->with(['seller:id,name', 'category', 'primaryImage', 'images'])
                ->withCount(['bids', 'watchers'])
                ->orderByDesc('created_at')
                ->limit(12)
                ->get();
        });
    }

    /** @return Collection<int, Auction> */
    public function mostWatched(): Collection
    {
        return Cache::remember('homepage:most_watched', self::TTL, function () {
            return Auction::query()
                ->where('status', AuctionStatus::Active->value)
                ->with(['seller:id,name', 'category', 'primaryImage', 'images'])
                ->withCount(['bids', 'watchers'])
                ->orderByDesc('watchers_count')
                ->limit(12)
                ->get();
        });
    }

    /**
     * All four sections at once — useful for a single homepage API call.
     *
     * @return array{featured: Collection<int, Auction>, ending_soon: Collection<int, Auction>, new_arrivals: Collection<int, Auction>, most_watched: Collection<int, Auction>}
     */
    public function all(): array
    {
        return [
            'featured'     => $this->featured(),
            'ending_soon'  => $this->endingSoon(),
            'new_arrivals' => $this->newArrivals(),
            'most_watched' => $this->mostWatched(),
        ];
    }

    public function invalidate(): void
    {
        Cache::forget('homepage:featured');
        Cache::forget('homepage:ending_soon');
        Cache::forget('homepage:new_arrivals');
        Cache::forget('homepage:most_watched');
    }
}
