<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\AuctionWatcher;
use App\Notifications\AuctionEndingSoonNotification;
use Illuminate\Console\Command;

class SendEndingSoonNotifications extends Command
{
    protected $signature = 'notifications:ending-soon';

    protected $description = 'Notify watchers of auctions ending within 1 hour';

    public function handle(): int
    {
        $endingSoon = Auction::query()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->where('ends_at', '<=', now()->addHour())
            ->get();

        $sent = 0;

        foreach ($endingSoon as $auction) {
            // Only send the notification once — flag it via cache
            $cacheKey = "ending_soon_notified:{$auction->id}";

            if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                continue;
            }

            $watchers = AuctionWatcher::query()
                ->with('user')
                ->where('auction_id', $auction->id)
                ->get();

            foreach ($watchers as $watcher) {
                if ($watcher->user) {
                    $watcher->user->notify(new AuctionEndingSoonNotification($auction));
                    $sent++;
                }
            }

            // Cache for 90 minutes so we don't spam if scheduler runs again before auction ends
            \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addMinutes(90));
        }

        if ($sent > 0) {
            $this->info("Sent {$sent} ending-soon notification(s).");
        }

        return self::SUCCESS;
    }
}
