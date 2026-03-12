<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Auction;
use App\Models\User;
use App\Notifications\AuctionEndingSoonNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * T-1403: Notify users watching auctions that end within 2 hours.
 * Dispatched by `notifications:ending-soon` scheduled command (every 15 min).
 */
class WishlistReminderJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries   = 2;
    public int $timeout = 120;

    public function handle(): void
    {
        // Find active auctions ending within 2 hours that haven't been reminded in last 2h
        Auction::query()
            ->where('status', 'active')
            ->whereBetween('ends_at', [now(), now()->addHours(2)])
            ->with([
                'watchers' => fn ($q) => $q->with('user:id,name,email,notification_preferences'),
            ])
            ->chunk(50, function ($auctions) {
                foreach ($auctions as $auction) {
                    foreach ($auction->watchers as $watcher) {
                        $user = $watcher->user ?? null;

                        if (! $user instanceof User) {
                            continue;
                        }

                        // Throttle: one reminder per auction per user per 2 hours
                        $cacheKey = "wishlist_reminder:{$user->id}:{$auction->id}";

                        if (cache()->has($cacheKey)) {
                            continue;
                        }

                        if ($user->prefersEmailNotification('ending_soon')) {
                            $user->notify(new AuctionEndingSoonNotification($auction));
                        }

                        cache()->put($cacheKey, true, 7200);
                    }
                }
            });
    }
}
