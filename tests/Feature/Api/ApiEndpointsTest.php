<?php

/**
 * ===================================
 * API FEATURE TESTS
 * ===================================
 * Tests for API endpoints
 */

declare(strict_types=1);

use App\Models\Auction;
use App\Models\Bid;
use App\Models\Category;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

// ============================================================================
// AUCTION API TESTS
// ============================================================================

describe('Auction API', function () {

    it('lists active auctions with pagination', function () {
        Auction::factory()->active()->count(20)->create();

        $response = $this->getJson('/api/v1/auctions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'current_price', 'ends_at'],
                ],
                'meta' => ['current_page', 'per_page', 'total'],
            ]);
    });

    it('filters auctions by category', function () {
        $category = Category::factory()->create();
        Auction::factory()->active()->count(5)->create(['category_id' => $category->id]);
        Auction::factory()->active()->count(5)->create(['category_id' => null]);

        $response = $this->getJson('/api/v1/auctions?category_id='.$category->id);

        $response->assertStatus(200);
        expect(count($response->json('data')))->toBe(5);
    });

    it('filters by price range', function () {
        Auction::factory()->active()->create(['current_price' => 50]);
        Auction::factory()->active()->create(['current_price' => 150]);
        Auction::factory()->active()->create(['current_price' => 250]);

        $response = $this->getJson('/api/v1/auctions?price_min=100&price_max=200');

        $response->assertStatus(200);
        expect(count($response->json('data')))->toBe(1);
    });

    it('searches by keyword', function () {
        Auction::factory()->active()->create(['title' => 'Samsung Galaxy S23']);
        Auction::factory()->active()->create(['title' => 'iPhone 14 Pro']);
        Auction::factory()->active()->create(['title' => 'Samsung Watch']);

        $response = $this->getJson('/api/v1/auctions?search=Samsung');

        $response->assertStatus(200);
        expect(count($response->json('data')))->toBe(2);
    });

    it('sorts by ending soon', function () {
        $soon = Auction::factory()->active()->create(['ends_at' => now()->addHour()]);
        $later = Auction::factory()->active()->create(['ends_at' => now()->addDays(5)]);

        $response = $this->getJson('/api/v1/auctions?sort=ending_soon');

        $response->assertStatus(200);
        expect($response->json('data.0.id'))->toBe($soon->id);
    });

    it('creates auction as authenticated seller', function () {
        $seller = User::factory()->seller()->create();
        Sanctum::actingAs($seller);

        $response = $this->postJson('/api/v1/seller/auctions', [
            'title' => 'Test Auction',
            'description' => 'Test description',
            'category_id' => 1,
            'start_price' => 100,
            'duration_days' => 7,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'title', 'status']);
    });

    it('rejects auction creation over tier limit', function () {
        // Create seller with Free tier (5 auction limit)
        $seller = User::factory()->seller()->create();
        Auction::factory()->active()->count(5)->create(['seller_id' => $seller->id]);
        Sanctum::actingAs($seller);

        $response = $this->postJson('/api/v1/seller/auctions', [
            'title' => 'Another Auction',
            'description' => 'Test',
            'start_price' => 100,
        ]);

        $response->assertStatus(403)
            ->assertJson(['error' => 'Auction limit reached']);
    });

    it('cancels auction with no bids', function () {
        $seller = User::factory()->seller()->create();
        $auction = Auction::factory()->active()->create([
            'seller_id' => $seller->id,
            'bids_count' => 0,
        ]);
        Sanctum::actingAs($seller);

        $response = $this->deleteJson("/api/v1/seller/auctions/{$auction->id}");

        $response->assertStatus(200);
        expect($auction->fresh()->status)->toBe('cancelled');
    });

    it('rejects cancel when bids exist', function () {
        $seller = User::factory()->seller()->create();
        $auction = Auction::factory()->active()->create([
            'seller_id' => $seller->id,
            'bids_count' => 1,
        ]);
        Sanctum::actingAs($seller);

        $response = $this->deleteJson("/api/v1/seller/auctions/{$auction->id}");

        $response->assertStatus(403);
    });
});

// ============================================================================
// BID API TESTS
// ============================================================================

describe('Bid API', function () {

    it('returns 200 and bid data on success', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user = User::factory()->buyer()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/auctions/{$auction->id}/bid", [
            'amount' => 105,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'amount', 'user_id']);
    });

    it('returns 400 when bid below minimum', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user = User::factory()->buyer()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/auctions/{$auction->id}/bid", [
            'amount' => 101,
        ]);

        $response->assertStatus(400);
    });

    it('returns 401 when not authenticated', function () {
        $auction = Auction::factory()->active()->create();

        $response = $this->postJson("/api/v1/auctions/{$auction->id}/bid", [
            'amount' => 105,
        ]);

        $response->assertStatus(401);
    });

    it('returns 403 when bidding on own auction', function () {
        $seller = User::factory()->seller()->create();
        $auction = Auction::factory()->active()->create(['seller_id' => $seller->id]);
        Sanctum::actingAs($seller);

        $response = $this->postJson("/api/v1/auctions/{$auction->id}/bid", [
            'amount' => 105,
        ]);

        $response->assertStatus(403);
    });

    it('returns 410 when auction ended', function () {
        $auction = Auction::factory()->finished()->create();
        $user = User::factory()->buyer()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/auctions/{$auction->id}/bid", [
            'amount' => 105,
        ]);

        $response->assertStatus(410);
    });

    it('returns 422 on validation error', function () {
        $auction = Auction::factory()->active()->create();
        $user = User::factory()->buyer()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/auctions/{$auction->id}/bid", [
            'amount' => 'invalid',
        ]);

        $response->assertStatus(422);
    });

    it('returns 429 when rate limited', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user = User::factory()->buyer()->create();
        Sanctum::actingAs($user);

        // Make 11 rapid bids (limit is 10/min)
        for ($i = 0; $i < 11; $i++) {
            $this->postJson("/api/v1/auctions/{$auction->id}/bid", [
                'amount' => 100 + ($i * 5),
            ]);
        }

        $response = $this->postJson("/api/v1/auctions/{$auction->id}/bid", [
            'amount' => 160,
        ]);

        $response->assertStatus(429);
    });
});

// ============================================================================
// WALLET API TESTS
// ============================================================================

describe('Wallet API', function () {

    it('returns wallet balance', function () {
        $user = User::factory()->create();
        Wallet::factory()->create(['user_id' => $user->id, 'balance' => 250.50]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/user/wallet');

        $response->assertStatus(200)
            ->assertJson(['balance' => 250.50]);
    });

    it('returns transaction history', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id]);
        WalletTransaction::factory()->count(5)->create(['wallet_id' => $wallet->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/user/wallet/transactions');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    });
});

// ============================================================================
// AUTH API TESTS
// ============================================================================

describe('Auth API', function () {

    it('registers new buyer', function () {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test Buyer',
            'email' => 'buyer@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'type' => 'buyer',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token']);
    });

    it('registers new seller', function () {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test Seller',
            'email' => 'seller@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'type' => 'seller',
        ]);

        $response->assertStatus(201);
    });

    it('rejects duplicate email', function () {
        User::factory()->create(['email' => 'test@test.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'Password123!',
            'type' => 'buyer',
        ]);

        $response->assertStatus(422);
    });

    it('logs in with correct credentials', function () {
        $user = User::factory()->create(['email' => 'test@test.com', 'password' => bcrypt('Password123!')]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@test.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);
    });

    it('rejects wrong password', function () {
        $user = User::factory()->create(['email' => 'test@test.com']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@test.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(401);
    });

    it('returns authenticated user info', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('user.email', $user->email);
    });

    it('logs out user', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);
    });
});
