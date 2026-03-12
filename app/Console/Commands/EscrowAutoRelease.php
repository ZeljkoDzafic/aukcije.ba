<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\EscrowService;
use Illuminate\Console\Command;

class EscrowAutoRelease extends Command
{
    protected $signature = 'escrow:auto-release';

    protected $description = 'Auto-release escrow funds for delivered orders older than configured days';

    public function handle(EscrowService $escrowService): int
    {
        $count = $escrowService->autoRelease();
        $this->info("Auto-released escrow for {$count} order(s).");

        return self::SUCCESS;
    }
}
