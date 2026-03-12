<?php

namespace App\Listeners;

use App\Events\BidPlaced;
use Illuminate\Support\Facades\Cache;

class BroadcastBidUpdate
{
    public function handle(BidPlaced $event): void
    {
        // Cache current price for fast reads (avoids DB hit on every page load)
        Cache::put(
            "auction:{$event->auction->id}:current_price",
            $event->auction->current_price,
            now()->addHours(2)
        );
    }
}
