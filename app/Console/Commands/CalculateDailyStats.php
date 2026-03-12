<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CalculateDailyStats extends Command
{
    protected $signature = 'analytics:daily-stats';

    protected $description = 'Calculate and cache daily platform statistics';

    public function handle(): int
    {
        $date = now()->subDay()->toDateString();

        $stats = [
            'date' => $date,
            'new_users' => User::query()->whereDate('created_at', $date)->count(),
            'new_auctions' => Auction::query()->whereDate('created_at', $date)->count(),
            'bids_placed' => Bid::query()->whereDate('created_at', $date)->count(),
            'orders_created' => Order::query()->whereDate('created_at', $date)->count(),
            'revenue' => (float) Order::query()
                ->whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('commission_amount'),
            'calculated_at' => now()->toIso8601String(),
        ];

        Cache::put("daily_stats:{$date}", $stats, now()->addDays(90));

        $this->info("Daily stats for {$date} calculated and cached.");

        return self::SUCCESS;
    }
}
