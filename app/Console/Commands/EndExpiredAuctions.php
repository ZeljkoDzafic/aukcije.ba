<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\EndAuctionJob;
use App\Models\Auction;
use Illuminate\Console\Command;

class EndExpiredAuctions extends Command
{
    protected $signature = 'auctions:end-expired';

    protected $description = 'End all active auctions that have expired';

    public function handle(): int
    {
        $expired = Auction::where('status', 'active')
            ->where('ends_at', '<=', now())
            ->get();

        foreach ($expired as $auction) {
            EndAuctionJob::dispatch($auction);
        }

        if ($expired->count() > 0) {
            $this->info("Dispatched EndAuctionJob for {$expired->count()} auction(s).");
        }

        return self::SUCCESS;
    }
}
