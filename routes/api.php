<?php

use Illuminate\Http\Request;
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

    /*
    |--------------------------------------------------------------------------
    | Public API Routes
    |--------------------------------------------------------------------------
    */

    // Auctions
    Route::get('/auctions', [App\Http\Controllers\Api\AuctionController::class, 'index']);
    Route::get('/auctions/{auction}', [App\Http\Controllers\Api\AuctionController::class, 'show']);

    // Categories
    Route::get('/categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);

    // Search
    Route::get('/search', [App\Http\Controllers\Api\SearchController::class, 'search']);

    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
    |--------------------------------------------------------------------------
    */

    Route::post('/auth/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/auth/login', [App\Http\Controllers\Api\AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
        Route::get('/auth/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
    });

    /*
    |--------------------------------------------------------------------------
    | Authenticated User Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        // Bidding
        Route::post('/auctions/{auction}/bid', [App\Http\Controllers\Api\BidController::class, 'place']);
        Route::get('/auctions/{auction}/bids', [App\Http\Controllers\Api\BidController::class, 'index']);

        // Watchlist
        Route::get('/watchlist', [App\Http\Controllers\Api\WatchlistController::class, 'index']);
        Route::post('/watchlist/{auction}', [App\Http\Controllers\Api\WatchlistController::class, 'add']);
        Route::delete('/watchlist/{auction}', [App\Http\Controllers\Api\WatchlistController::class, 'remove']);

        // User Profile
        Route::get('/user/profile', [App\Http\Controllers\Api\UserController::class, 'profile']);
        Route::put('/user/profile', [App\Http\Controllers\Api\UserController::class, 'update']);

        // Wallet
        Route::get('/user/wallet', [App\Http\Controllers\Api\WalletController::class, 'balance']);
        Route::post('/user/wallet/deposit', [App\Http\Controllers\Api\WalletController::class, 'deposit']);
        Route::post('/user/wallet/withdraw', [App\Http\Controllers\Api\WalletController::class, 'withdraw']);
        Route::get('/user/wallet/transactions', [App\Http\Controllers\Api\WalletController::class, 'transactions']);

        // Orders
        Route::get('/user/orders', [App\Http\Controllers\Api\OrderController::class, 'index']);
        Route::get('/user/orders/{order}', [App\Http\Controllers\Api\OrderController::class, 'show']);

        // Ratings
        Route::post('/orders/{order}/rate', [App\Http\Controllers\Api\RatingController::class, 'rate']);

        // Messages
        Route::get('/user/messages', [App\Http\Controllers\Api\MessageController::class, 'index']);
        Route::post('/user/messages', [App\Http\Controllers\Api\MessageController::class, 'store']);
    });

    /*
    |--------------------------------------------------------------------------
    | Seller Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware(['auth:sanctum', 'role:seller,verified_seller'])->prefix('seller')->group(function () {

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
        Route::get('/disputes', [App\Http\Controllers\Api\Admin\DisputeController::class, 'index']);
        Route::get('/disputes/{dispute}', [App\Http\Controllers\Api\Admin\DisputeController::class, 'show']);
        Route::post('/disputes/{dispute}/resolve', [App\Http\Controllers\Api\Admin\DisputeController::class, 'resolve']);

        // Statistics
        Route::get('/statistics', [App\Http\Controllers\Api\Admin\StatisticsController::class, 'index']);
    });
});
