<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\AuctionEnded;
use App\Notifications\AuctionEndedNotification;

class NotifyWatchers
{
    public function handle(AuctionEnded $event): void
    {
        $event->auction->watchers()
            ->with('user')
            ->get()
            ->each(function ($watcher) use ($event) {
                $watcher->user?->notify(
                    new AuctionEndedNotification($event->auction)
                );
            });
    }
}
