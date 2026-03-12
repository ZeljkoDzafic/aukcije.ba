<?php

declare(strict_types=1);

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

        // TODO: Also cache minimum_bid and ends_at so AuctionController::show()
        //       can serve these hot values without a DB query:
        //       Cache::put("auction:{$id}:minimum_bid", ..., ...)
        //       Cache::put("auction:{$id}:ends_at", ..., ...)
        // TODO: The BidPlaced event itself is already Broadcastable — verify that
        //       the Reverb server is running and the 'auction.{id}' channel receives
        //       the broadcast. Add integration test asserting BidPlaced::broadcastOn()
        //       returns PublicChannel('auction.'.$auctionId).
    }
}
