<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Auction;
use App\Models\SavedSearch;
use App\Models\User;
use App\Notifications\AuctionEndingSoonNotification;
use Illuminate\Support\Collection;

/**
 * T-1201: Saved searches and new-auction alerting.
 *
 * Users can save searches. Every 4 hours a job matches new auctions
 * against saved searches and notifies matching users.
 */
class SavedSearchService
{
    /**
     * Save a search for a user.
     *
     * @param array<string, mixed> $query Filters: q, category_id, price_min, price_max, condition, location
     */
    public function save(User $user, string $name, array $query): SavedSearch
    {
        return SavedSearch::create([
            'user_id' => $user->id,
            'name'    => $name,
            'query'   => $query,
        ]);
    }

    /**
     * @return Collection<int, SavedSearch>
     */
    public function forUser(User $user): Collection
    {
        return SavedSearch::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function delete(SavedSearch $search): void
    {
        $search->delete();
    }

    /**
     * Process all saved searches against new auctions created since last run.
     * Called by the `searches:send-alerts` scheduled command.
     */
    public function sendAlerts(): int
    {
        $notified = 0;

        // Get auctions created in the last 4 hours + 5 min buffer
        $newAuctions = Auction::query()
            ->where('created_at', '>=', now()->subHours(4)->subMinutes(5))
            ->with(['images', 'category'])
            ->get();

        if ($newAuctions->isEmpty()) {
            return 0;
        }

        SavedSearch::query()
            ->with('user')
            ->chunk(200, function (Collection $searches) use ($newAuctions, &$notified) {
                foreach ($searches as $search) {
                    if (! $search->user) {
                        continue;
                    }

                    $matches = $newAuctions->filter(fn (Auction $a) => $this->matches($a, $search->query));

                    if ($matches->isNotEmpty()) {
                        foreach ($matches as $auction) {
                            $search->user->notify(new AuctionEndingSoonNotification($auction));
                            $notified++;
                        }

                        $search->updateQuietly(['last_notified_at' => now()]);
                    }
                }
            });

        return $notified;
    }

    /**
     * @param array<string, mixed> $query
     */
    private function matches(Auction $auction, array $query): bool
    {
        if (! empty($query['q'])) {
            $term = mb_strtolower($query['q']);
            if (! str_contains(mb_strtolower($auction->title ?? ''), $term)) {
                return false;
            }
        }

        if (! empty($query['category_id']) && $auction->category_id !== $query['category_id']) {
            return false;
        }

        if (! empty($query['price_min']) && $auction->current_price < (float) $query['price_min']) {
            return false;
        }

        if (! empty($query['price_max']) && $auction->current_price > (float) $query['price_max']) {
            return false;
        }

        if (! empty($query['condition']) && $auction->condition !== $query['condition']) {
            return false;
        }

        return true;
    }
}
