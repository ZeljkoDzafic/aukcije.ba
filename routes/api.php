<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Admin\DisputeController;
use App\Http\Controllers\Api\Admin\StatisticsController;
use App\Http\Controllers\Api\AuctionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BidController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\SearchController;
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

    Route::middleware(['auth:sanctum', 'role:seller|verified_seller'])->prefix('seller')->group(function () {

        // Create Auction
        Route::post('/auctions', [App\Http\Controllers\Api\Seller\AuctionController::class, 'store']);
        Route::put('/auctions/{auction}', [App\Http\Controllers\Api\Seller\AuctionController::class, 'update']);
        Route::delete('/auctions/{auction}', [App\Http\Controllers\Api\Seller\AuctionController::class, 'destroy']);

        // Orders
        Route::get('/orders', [App\Http\Controllers\Api\Seller\OrderController::class, 'index']);
        Route::post('/orders/{order}/ship', [App\Http\Controllers\Api\Seller\OrderController::class, 'ship']);
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware(['auth:sanctum', 'role:admin|moderator'])->prefix('admin')->group(function () {

        // Users
        Route::get('/users', [App\Http\Controllers\Api\Admin\UserController::class, 'index']);
        Route::get('/users/{user}', [App\Http\Controllers\Api\Admin\UserController::class, 'show']);
        Route::put('/users/{user}/role', [App\Http\Controllers\Api\Admin\UserController::class, 'updateRole']);
        Route::post('/users/{user}/ban', [App\Http\Controllers\Api\Admin\UserController::class, 'ban']);

        // Auctions
        Route::get('/auctions', [App\Http\Controllers\Api\Admin\AuctionController::class, 'index']);
        Route::put('/auctions/{auction}/approve', [App\Http\Controllers\Api\Admin\AuctionController::class, 'approve']);
        Route::put('/auctions/{auction}/reject', [App\Http\Controllers\Api\Admin\AuctionController::class, 'reject']);
        Route::put('/auctions/{auction}/feature', [App\Http\Controllers\Api\Admin\AuctionController::class, 'feature']);

        // Categories
        Route::apiResource('/categories', App\Http\Controllers\Api\Admin\CategoryController::class);

        // Disputes
        Route::get('/disputes', [DisputeController::class, 'index']);
        Route::get('/disputes/{dispute}', [DisputeController::class, 'show']);
        Route::post('/disputes/{dispute}/resolve', [DisputeController::class, 'resolve']);

        // Statistics
        Route::get('/statistics', [StatisticsController::class, 'index']);
    });
});
