<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Admin\AnalyticsController;
use App\Http\Controllers\Api\Admin\DisputeController;
use App\Http\Controllers\Api\Admin\KycBackofficeController;
use App\Http\Controllers\Api\Admin\ModerationController;
use App\Http\Controllers\Api\Admin\StatisticsController;
use App\Http\Controllers\Api\AuctionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BidController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CourierWebhookController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SellerDirectoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WatchlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
|
| Base URL: /api/v1
|
*/

Route::prefix('v1')->group(function () {

    // Courier webhook callbacks — no auth, signature verified in controller
    Route::post('/webhooks/courier/{courier}', [CourierWebhookController::class, 'handle'])
        ->name('webhooks.courier')
        ->withoutMiddleware(['auth:sanctum']);

    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Public API Routes
    |--------------------------------------------------------------------------
    */

    // Auctions
    Route::get('/auctions', [AuctionController::class, 'index']);
    Route::get('/auctions/{auction}', [AuctionController::class, 'show']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);

    // Search
    Route::get('/search', [SearchController::class, 'search']);

    // Homepage sections (featured, ending_soon, new_arrivals, most_watched)
    Route::get('/homepage', [App\Http\Controllers\Api\HomepageController::class, 'index']);

    // Public seller directory
    Route::get('/sellers', [SellerDirectoryController::class, 'index']);
    Route::get('/sellers/{user}', [SellerDirectoryController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
    |--------------------------------------------------------------------------
    */

    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
    });

    /*
    |--------------------------------------------------------------------------
    | Authenticated User Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        // Bidding
        Route::post('/auctions/{auction}/bid', [BidController::class, 'place'])->middleware('throttle.bids');
        Route::get('/auctions/{auction}/bids', [BidController::class, 'index']);

        // Watchlist
        Route::get('/watchlist', [WatchlistController::class, 'index']);
        Route::post('/watchlist/{auction}', [WatchlistController::class, 'add']);
        Route::delete('/watchlist/{auction}', [WatchlistController::class, 'remove']);

        // User Profile
        Route::get('/user/profile', [UserController::class, 'profile']);
        Route::put('/user/profile', [UserController::class, 'update']);

        // Wallet
        Route::get('/user/wallet', [WalletController::class, 'balance']);
        Route::post('/user/wallet/deposit', [WalletController::class, 'deposit']);
        Route::post('/user/wallet/withdraw', [WalletController::class, 'withdraw']);
        Route::get('/user/wallet/transactions', [WalletController::class, 'transactions']);

        // Orders
        Route::get('/user/orders', [OrderController::class, 'index']);
        Route::get('/user/orders/{order}', [OrderController::class, 'show']);

        // Ratings
        Route::post('/orders/{order}/rate', [RatingController::class, 'rate']);

        // Messages
        Route::get('/user/messages', [MessageController::class, 'index']);
        Route::post('/user/messages', [MessageController::class, 'store']);
    });

    /*
    |--------------------------------------------------------------------------
    | Seller Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware(['auth:sanctum', 'role:seller|verified_seller', 'audit.trail'])->prefix('seller')->group(function () {

        // Create Auction
        Route::post('/auctions', [App\Http\Controllers\Api\Seller\AuctionController::class, 'store']);
        Route::put('/auctions/{auction}', [App\Http\Controllers\Api\Seller\AuctionController::class, 'update']);
        Route::delete('/auctions/{auction}', [App\Http\Controllers\Api\Seller\AuctionController::class, 'destroy']);
        Route::post('/auctions/{auction}/schedule', [App\Http\Controllers\Api\Seller\AuctionController::class, 'schedule']);
        Route::post('/auctions/{auction}/second-chance', [App\Http\Controllers\Api\Seller\SecondChanceController::class, 'offer']);

        // Orders
        Route::get('/orders', [App\Http\Controllers\Api\Seller\OrderController::class, 'index']);
        Route::post('/orders/{order}/ship', [App\Http\Controllers\Api\Seller\OrderController::class, 'ship']);

        // Stats (T-1300)
        Route::get('/stats', [App\Http\Controllers\Api\Seller\StatsController::class, 'index']);

        // Templates (T-1301)
        Route::get('/templates', [App\Http\Controllers\Api\Seller\TemplateController::class, 'index']);
        Route::post('/templates', [App\Http\Controllers\Api\Seller\TemplateController::class, 'store']);
        Route::post('/templates/{template}/create-auction', [App\Http\Controllers\Api\Seller\TemplateController::class, 'createAuction']);
        Route::delete('/templates/{template}', [App\Http\Controllers\Api\Seller\TemplateController::class, 'destroy']);

        // Bulk operations (T-1302)
        Route::post('/bulk/publish', [App\Http\Controllers\Api\Seller\BulkController::class, 'publishDrafts']);
        Route::post('/bulk/end', [App\Http\Controllers\Api\Seller\BulkController::class, 'endActive']);
        Route::post('/bulk/clone', [App\Http\Controllers\Api\Seller\BulkController::class, 'clone']);
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware(['auth:sanctum', 'role:admin|moderator', 'audit.trail'])->prefix('admin')->group(function () {

        // Users
        Route::get('/users', [App\Http\Controllers\Api\Admin\UserController::class, 'index']);
        Route::get('/users/{user}', [App\Http\Controllers\Api\Admin\UserController::class, 'show']);
        Route::put('/users/{user}/role', [App\Http\Controllers\Api\Admin\UserController::class, 'updateRole']);
        Route::post('/users/{user}/ban', [App\Http\Controllers\Api\Admin\UserController::class, 'ban']);

        // Auctions (individual)
        Route::get('/auctions', [App\Http\Controllers\Api\Admin\AuctionController::class, 'index']);
        Route::put('/auctions/{auction}/approve', [App\Http\Controllers\Api\Admin\AuctionController::class, 'approve']);
        Route::put('/auctions/{auction}/reject', [App\Http\Controllers\Api\Admin\AuctionController::class, 'reject']);
        Route::put('/auctions/{auction}/feature', [App\Http\Controllers\Api\Admin\AuctionController::class, 'feature']);

        // Bulk moderation (T-1500)
        Route::post('/auctions/bulk/approve', [ModerationController::class, 'bulkApprove']);
        Route::post('/auctions/bulk/reject', [ModerationController::class, 'bulkReject']);
        Route::post('/auctions/bulk/feature', [ModerationController::class, 'bulkFeature']);

        // Categories
        Route::apiResource('/categories', App\Http\Controllers\Api\Admin\CategoryController::class);
        Route::post('/categories/reorder', [App\Http\Controllers\Api\Admin\CategoryController::class, 'reorder']);
        Route::post('/categories/{category}/feature', [App\Http\Controllers\Api\Admin\CategoryController::class, 'setFeatured']);

        // KYC Backoffice (T-1501)
        Route::get('/kyc', [KycBackofficeController::class, 'index']);
        Route::get('/kyc/users/{user}', [KycBackofficeController::class, 'show']);
        Route::post('/kyc/users/{user}/approve', [KycBackofficeController::class, 'approve']);
        Route::post('/kyc/users/{user}/reject', [KycBackofficeController::class, 'reject']);

        // Disputes
        Route::get('/disputes', [DisputeController::class, 'index']);
        Route::get('/disputes/{dispute}', [DisputeController::class, 'show']);
        Route::post('/disputes/{dispute}/resolve', [DisputeController::class, 'resolve']);

        // Statistics (legacy)
        Route::get('/statistics', [StatisticsController::class, 'index']);

        // Analytics (T-1503)
        Route::get('/analytics', [AnalyticsController::class, 'index']);
    });
});
