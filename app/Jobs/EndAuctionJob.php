<?php

namespace App\Jobs;

use App\Models\Auction;
use App\Services\AuctionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EndAuctionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public readonly Auction $auction) {}

    public function handle(AuctionService $auctionService): void
    {
        $fresh = $this->auction->fresh();

        if (!$fresh || $fresh->status !== 'active') {
            return;
        }

        try {
            $auctionService->endAuction($fresh);
        } catch (\Throwable $e) {
            Log::error("EndAuctionJob failed for auction {$this->auction->id}: {$e->getMessage()}");
            throw $e;
        }
    }
}
