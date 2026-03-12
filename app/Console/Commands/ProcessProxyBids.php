<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\Bid;
use App\Services\BiddingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessProxyBids extends Command
{
    protected $signature = 'bidding:process-proxy';

    protected $description = 'Re-process proxy bids for active auctions that may have stale proxy state';

    public function handle(BiddingService $biddingService): int
    {
        $auctions = Auction::query()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->whereHas('proxyBids', fn ($q) => $q->where('is_active', true))
            ->get();

        foreach ($auctions as $auction) {
            /** @var Bid|null $winningBid */
            $winningBid = $auction->bids()->where('is_winning', true)->first();

            if (! $winningBid) {
                continue;
            }

            try {
                $biddingService->processProxyBids($auction, $winningBid);
            } catch (\Throwable $e) {
                Log::error("ProcessProxyBids failed for auction {$auction->id}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
