<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\AuctionTemplateService;
use App\Services\BulkAuctionService;
use App\Services\BulkModerationService;
use App\Services\CategoryMerchandizingService;
use App\Services\Couriers\BhPostaCourier;
use App\Services\Couriers\EuroExpressCourier;
use App\Services\Couriers\PostExpressCourier;
use App\Services\FraudScoringService;
use App\Services\GDPRErasureService;
use App\Services\ImageOptimizationService;
use App\Services\Gateways\CorvusPayGateway;
use App\Services\Gateways\MonriGateway;
use App\Services\Gateways\StripeGateway;
use App\Services\Gateways\WalletGateway;
use App\Services\HomepageDataService;
use App\Services\SavedSearchService;
use App\Services\SecondChanceOfferService;
use App\Services\SellerReputationService;
use App\Services\ShillBiddingDetector;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Payment gateways — registered as singletons so config is read once
        $this->app->singleton(StripeGateway::class);
        $this->app->singleton(MonriGateway::class);
        $this->app->singleton(CorvusPayGateway::class);
        $this->app->singleton(WalletGateway::class);

        // Courier adapters — registered as singletons
        $this->app->singleton(EuroExpressCourier::class);
        $this->app->singleton(PostExpressCourier::class);
        $this->app->singleton(BhPostaCourier::class);

        // Trust & Safety services
        $this->app->singleton(FraudScoringService::class);
        $this->app->singleton(ShillBiddingDetector::class);
        $this->app->singleton(SellerReputationService::class);

        // Discovery & marketplace services
        $this->app->singleton(HomepageDataService::class);
        $this->app->singleton(SavedSearchService::class);

        // Seller tools
        $this->app->singleton(AuctionTemplateService::class);
        $this->app->singleton(BulkAuctionService::class);
        $this->app->singleton(SecondChanceOfferService::class);

        // Admin operations
        $this->app->singleton(BulkModerationService::class);
        $this->app->singleton(CategoryMerchandizingService::class);

        // GDPR
        $this->app->singleton(GDPRErasureService::class);

        // Media / image pipeline
        $this->app->singleton(ImageOptimizationService::class);
    }

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $limit = (int) config('app.rate_limit_api', env('RATE_LIMIT_API', 100));

            return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
        });
    }
}
