<?php
namespace App\Providers;

use App\Models\FeatureFlag;
use App\Models\{Auction, Order};
use App\Policies\{AuctionPolicy, OrderPolicy};
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Auction::class => AuctionPolicy::class,
        Order::class   => OrderPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Blade::if('feature', fn (string $flag): bool => FeatureFlag::isActive($flag));
    }
}
