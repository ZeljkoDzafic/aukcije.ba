<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SellerReputationJob;
use Illuminate\Console\Command;

class CalculateSellerReputations extends Command
{
    protected $signature   = 'sellers:recalculate-reputation';
    protected $description = 'Recalculate seller reputation scores for all active sellers (T-1104)';

    public function handle(): int
    {
        $this->info('Dispatching SellerReputationJob...');

        SellerReputationJob::dispatch();

        $this->info('Done.');

        return self::SUCCESS;
    }
}
