<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\SellerReputationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * T-1104: Recalculate seller reputation scores for all active sellers.
 * Dispatched by `sellers:recalculate-reputation` scheduled command (daily).
 */
class SellerReputationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries   = 2;
    public int $timeout = 300;

    public function handle(SellerReputationService $service): void
    {
        $count = $service->recalculateAll();

        Log::info("SellerReputationJob: recalculated {$count} seller reputation scores.");
    }
}
