<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Auction;
use App\Models\FeatureFlag;
use App\Models\Order;
use App\Policies\AuctionPolicy;
use App\Policies\OrderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Auction::class => AuctionPolicy::class,
        Order::class => OrderPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Blade::if('feature', fn (string $flag): bool => FeatureFlag::isActive($flag));
    }
}
