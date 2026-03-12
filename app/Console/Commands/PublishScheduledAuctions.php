<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Auction;
use App\Services\AuctionService;
use Illuminate\Console\Command;

/**
 * T-1303: Publish auctions whose scheduled start time has arrived.
 * Runs every minute via scheduler.
 */
class PublishScheduledAuctions extends Command
{
    protected $signature   = 'auctions:publish-scheduled';
    protected $description = 'Transition scheduled auctions to active when starts_at arrives';

    public function handle(AuctionService $auctionService): int
    {
        $count = 0;

        Auction::query()
            ->where('status', 'scheduled')
            ->where('starts_at', '<=', now())
            ->each(function (Auction $auction) use ($auctionService, &$count) {
                try {
                    $auctionService->publishAuction($auction);
                    $count++;
                } catch (\Throwable $e) {
                    $this->error("Failed to publish auction {$auction->id}: {$e->getMessage()}");
                }
            });

        if ($count > 0) {
            $this->info("Published {$count} scheduled auctions.");
        }

        return self::SUCCESS;
    }
}
