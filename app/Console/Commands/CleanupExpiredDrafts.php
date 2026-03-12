<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Auction;
use Illuminate\Console\Command;

class CleanupExpiredDrafts extends Command
{
    protected $signature = 'auctions:cleanup-drafts';

    protected $description = 'Delete draft auctions older than 30 days';

    public function handle(): int
    {
        $cutoff = now()->subDays(30);

        $deleted = Auction::query()
            ->where('status', 'draft')
            ->where('created_at', '<', $cutoff)
            ->delete();

        if ($deleted > 0) {
            $this->info("Deleted {$deleted} expired draft auction(s).");
        }

        return self::SUCCESS;
    }
}
