<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Auction;
use App\Models\SavedSearch;
use App\Services\MarketplaceNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class SendSavedSearchAlerts extends Command
{
    protected $signature = 'searches:send-alerts';

    protected $description = 'Send marketplace alerts for saved searches that match active auctions';

    public function handle(MarketplaceNotificationService $notifications): int
    {
        if (! Schema::hasTable('saved_searches') || ! Schema::hasTable('auctions')) {
            return self::SUCCESS;
        }

        $sent = 0;

        SavedSearch::query()
            ->with('user')
            ->where('alert_enabled', true)
            ->get()
            ->each(function (SavedSearch $savedSearch) use ($notifications, &$sent): void {
                $matchedAuction = Auction::query()
                    ->with('category')
                    ->active()
                    ->where('title', 'like', '%'.$savedSearch->query.'%')
                    ->when($savedSearch->category_slug, fn ($query) => $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $savedSearch->category_slug)))
                    ->when($savedSearch->last_run_at, fn ($query) => $query->where('created_at', '>', $savedSearch->last_run_at))
                    ->latest('created_at')
                    ->first();

                if (! $matchedAuction || ! $savedSearch->user) {
                    $savedSearch->forceFill(['last_run_at' => now()])->save();

                    return;
                }

                $notifications->notify(
                    $savedSearch->user,
                    'saved_search_match',
                    'Pronađen novi artikal za tvoju pretragu',
                    "Nova aukcija '{$matchedAuction->title}' odgovara tvojoj spremljenoj pretrazi '{$savedSearch->query}'.",
                    [
                        'auction_id' => $matchedAuction->id,
                        'saved_search_id' => $savedSearch->id,
                    ]
                );

                $savedSearch->forceFill(['last_run_at' => now()])->save();
                $sent++;
            });

        if ($sent > 0) {
            $this->info("Sent {$sent} saved-search alert(s).");
        }

        return self::SUCCESS;
    }
}
