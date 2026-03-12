<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\AuctionEnded;
use App\Events\BidPlaced;
use App\Listeners\BroadcastBidUpdate;
use App\Listeners\NotifyWatchers;
use App\Listeners\SendOutbidNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BidPlaced::class => [
            BroadcastBidUpdate::class,
            SendOutbidNotification::class,
        ],
        AuctionEnded::class => [
            NotifyWatchers::class,
        ],
    ];

    public function boot(): void {}
}
