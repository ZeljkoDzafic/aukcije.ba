<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\AuctionEnded;
use App\Notifications\AuctionEndedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyWatchers implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public function handle(AuctionEnded $event): void
    {
        $winnerId = $event->auction->winner_id;

        $event->auction->watchers()
            ->with('user')
            ->when($winnerId, fn ($q) => $q->where('user_id', '!=', $winnerId))
            ->chunk(100, function ($chunk) use ($event) {
                foreach ($chunk as $watcher) {
                    if (! $watcher->user) {
                        continue;
                    }

                    if (! $watcher->user->prefersEmailNotification('auction_ended')) {
                        continue;
                    }

                    $watcher->user->notify(
                        new AuctionEndedNotification($event->auction)
                    );
                }
            });
    }
}
