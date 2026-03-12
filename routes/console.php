<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes (Scheduled Jobs)
|--------------------------------------------------------------------------
|
| Define all scheduled tasks here.
| See: docs/arhitektura/10-scheduled-jobs.md
|
*/

// End expired auctions - runs every minute
Schedule::command('auctions:end-expired')
    ->everyMinute()
    ->name('auctions:end-expired')
    ->onOneServer();

// Auto-release escrow funds - runs hourly
Schedule::command('escrow:auto-release')
    ->hourly()
    ->name('escrow:auto-release')
    ->onOneServer();

// Send payment reminders - runs every 6 hours
Schedule::command('orders:payment-reminders')
    ->everySixHours()
    ->name('orders:payment-reminders')
    ->onOneServer();

// Send shipping reminders - runs every 6 hours
Schedule::command('orders:shipping-reminders')
    ->everySixHours()
    ->name('orders:shipping-reminders')
    ->onOneServer();

// Reindex search - runs daily at 3 AM
Schedule::command('scout:import "App\\Models\\Auction"')
    ->dailyAt('03:00')
    ->name('search:reindex')
    ->onOneServer();

// Calculate daily statistics - runs daily at 1 AM
Schedule::command('analytics:daily-stats')
    ->dailyAt('01:00')
    ->name('analytics:daily-stats')
    ->onOneServer();

// Clean up expired drafts - runs daily at 2 AM
Schedule::command('auctions:cleanup-drafts')
    ->dailyAt('02:00')
    ->name('cleanup:expired-drafts')
    ->onOneServer();

// Send ending soon notifications - runs every 15 minutes
Schedule::command('notifications:ending-soon')
    ->everyFifteenMinutes()
    ->name('notifications:ending-soon')
    ->onOneServer();

// Send saved search alerts - runs hourly
Schedule::command('searches:send-alerts')
    ->hourly()
    ->name('searches:send-alerts')
    ->onOneServer();

// Process proxy bids - runs every minute
Schedule::command('bidding:process-proxy')
    ->everyMinute()
    ->name('bidding:process-proxy')
    ->onOneServer();

// SLO Monitoring - runs every 5 minutes
Schedule::job(new \App\Jobs\SloMonitoringJob())
    ->everyFiveMinutes()
    ->name('slo:monitoring')
    ->onOneServer();

// Clean up old sessions - runs daily at 4 AM
Schedule::command('session:table --prune=24')
    ->dailyAt('04:00')
    ->name('cleanup:sessions')
    ->onOneServer();

// Clean up old notification records - runs weekly
Schedule::command('notifications:table --prune=168')
    ->weekly()
    ->name('cleanup:notifications')
    ->onOneServer();

// Prune expired Sanctum tokens - runs weekly
Schedule::command('sanctum:prune-expired --hours=168')
    ->weekly()
    ->name('sanctum:prune-expired')
    ->onOneServer();

// T-1303: Publish scheduled auctions when starts_at arrives - runs every minute
Schedule::command('auctions:publish-scheduled')
    ->everyMinute()
    ->name('auctions:publish-scheduled')
    ->onOneServer();

// T-1104: Recalculate seller reputation scores - runs daily at 5 AM
Schedule::command('sellers:recalculate-reputation')
    ->dailyAt('05:00')
    ->name('sellers:recalculate-reputation')
    ->onOneServer();

// T-1306: Cancel orders that missed payment deadline - runs hourly
Schedule::command('orders:cancel-payment-deadline')
    ->hourly()
    ->name('orders:cancel-payment-deadline')
    ->onOneServer();
