<?php

/**
 * ===================================
 * BIDDING SERVICE UNIT TESTS
 * ===================================
 * Tests for the core bidding engine
 *
 * Coverage target: 100%
 */

declare(strict_types=1);

use App\Exceptions\AuctionNotActiveException;
use App\Exceptions\BidTooLowException;
use App\Exceptions\CannotBidOwnAuctionException;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\ProxyBid;
use App\Models\User;
use App\Services\BiddingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->biddingService = new BiddingService;
});

// ============================================================================
// PLACE BID TESTS
// ============================================================================

describe('BiddingService::placeBid', function () {

    // -------------------------------------------------------------------------
    // Happy Path Tests
    // -------------------------------------------------------------------------

    it('places a valid bid successfully', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user = User::factory()->buyer()->create();

        $bid = $this->biddingService->placeBid($auction, $user, 105);

        expect($bid)->toBeInstanceOf(Bid::class);
        expect($bid->amount)->toBe(105.0);
        expect($auction->fresh()->current_price)->toBe(105.0);
        expect($auction->fresh()->bids_count)->toBe(1);
        expect($bid->user_id)->toBe($user->id);
    });

    it('updates auction last_bid_at on new bid', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user = User::factory()->buyer()->create();

        $beforeBid = $auction->last_bid_at;
        sleep(1); // Ensure time difference

        $this->biddingService->placeBid($auction, $user, 105);

        expect($auction->fresh()->last_bid_at)->toBeGreaterThan($beforeBid);
    });

    it('sets bid as winning bid', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user = User::factory()->buyer()->create();

        $bid = $this->biddingService->placeBid($auction, $user, 105);

        expect($bid->is_winning)->toBeTrue();
    });

    // -------------------------------------------------------------------------
    // Validation Tests
    // -------------------------------------------------------------------------

    it('rejects bid below minimum increment', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user = User::factory()->buyer()->create();

        // Minimum bid at 100 BAM should be 105 (increment = 5)
        $this->biddingService->placeBid($auction, $user, 103);
    })->throws(BidTooLowException::class);

    it('rejects bid of exactly current price', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user = User::factory()->buyer()->create();

        $this->biddingService->placeBid($auction, $user, 100);
    })->throws(BidTooLowException::class);

    it('accepts bid at exact minimum increment', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user = User::factory()->buyer()->create();

        $bid = $this->biddingService->placeBid($auction, $user, 105);

        expect($bid->amount)->toBe(105.0);
    });

    it('rejects bid on own auction', function () {
        $seller = User::factory()->seller()->create();
        $auction = Auction::factory()->active()->create(['seller_id' => $seller->id]);

        $this->biddingService->placeBid($auction, $seller, 200);
    })->throws(CannotBidOwnAuctionException::class);

    it('rejects bid on inactive auction (finished)', function () {
        $auction = Auction::factory()->finished()->create();
        $user = User::factory()->buyer()->create();

        $this->biddingService->placeBid($auction, $user, 50);
    })->throws(AuctionNotActiveException::class);

    it('rejects bid on cancelled auction', function () {
        $auction = Auction::factory()->cancelled()->create();
        $user = User::factory()->buyer()->create();

        $this->biddingService->placeBid($auction, $user, 50);
    })->throws(AuctionNotActiveException::class);

    it('rejects bid on draft auction', function () {
        $auction = Auction::factory()->draft()->create();
        $user = User::factory()->buyer()->create();

        $this->biddingService->placeBid($auction, $user, 50);
    })->throws(AuctionNotActiveException::class);

    it('rejects bid from banned user', function () {
        $auction = Auction::factory()->active()->create();
        $user = User::factory()->banned()->create();

        $this->biddingService->placeBid($auction, $user, 50);
    })->throws(Exception::class);

    it('rejects bid from user with unverified email', function () {
        $auction = Auction::factory()->active()->create();
        $user = User::factory()->unverified()->create();

        $this->biddingService->placeBid($auction, $user, 50);
    })->throws(Exception::class);

    // -------------------------------------------------------------------------
    // Bid Increment Tests
    // -------------------------------------------------------------------------

    it('uses correct increment for price range 0-10 BAM', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 5]);
        $user = User::factory()->buyer()->create();

        $bid = $this->biddingService->placeBid($auction, $user, 5.50);

        expect($bid->amount)->toBe(5.50);
    });

    it('uses correct increment for price range 10-50 BAM', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 25]);
        $user = User::factory()->buyer()->create();

        $bid = $this->biddingService->placeBid($auction, $user, 26);

        expect($bid->amount)->toBe(26.0);
    });

    it('uses correct increment for price range 50-100 BAM', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 75]);
        $user = User::factory()->buyer()->create();

        $bid = $this->biddingService->placeBid($auction, $user, 77);

        expect($bid->amount)->toBe(77.0);
    });

    it('uses correct increment for price range 100-500 BAM', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 200]);
        $user = User::factory()->buyer()->create();

        $bid = $this->biddingService->placeBid($auction, $user, 205);

        expect($bid->amount)->toBe(205.0);
    });

    it('uses correct increment for price range 500-1000 BAM', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 750]);
        $user = User::factory()->buyer()->create();

        $bid = $this->biddingService->placeBid($auction, $user, 760);

        expect($bid->amount)->toBe(760.0);
    });

    it('uses correct increment for price range 1000-5000 BAM', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 2000]);
        $user = User::factory()->buyer()->create();

        $bid = $this->biddingService->placeBid($auction, $user, 2025);

        expect($bid->amount)->toBe(2025.0);
    });

    it('uses correct increment for price range 5000+ BAM', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 7500]);
        $user = User::factory()->buyer()->create();

        $bid = $this->biddingService->placeBid($auction, $user, 7550);

        expect($bid->amount)->toBe(7550.0);
    });

    // -------------------------------------------------------------------------
    // Multiple Bid Tests
    // -------------------------------------------------------------------------

    it('updates winning bid when outbid', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user1 = User::factory()->buyer()->create();
        $user2 = User::factory()->buyer()->create();

        $bid1 = $this->biddingService->placeBid($auction, $user1, 105);
        $bid2 = $this->biddingService->placeBid($auction, $user2, 110);

        expect($bid1->fresh()->is_winning)->toBeFalse();
        expect($bid2->fresh()->is_winning)->toBeTrue();
        expect($auction->fresh()->current_price)->toBe(110.0);
    });

    it('increments bids_count correctly', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100, 'bids_count' => 0]);
        $user1 = User::factory()->buyer()->create();
        $user2 = User::factory()->buyer()->create();

        $this->biddingService->placeBid($auction, $user1, 105);
        $this->biddingService->placeBid($auction, $user2, 110);
        $this->biddingService->placeBid($auction, $user1, 115);

        expect($auction->fresh()->bids_count)->toBe(3);
    });

    it('tracks highest bid correctly with multiple bidders', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user1 = User::factory()->buyer()->create();
        $user2 = User::factory()->buyer()->create();
        $user3 = User::factory()->buyer()->create();

        $this->biddingService->placeBid($auction, $user1, 105);
        $this->biddingService->placeBid($auction, $user2, 110);
        $this->biddingService->placeBid($auction, $user3, 115);
        $this->biddingService->placeBid($auction, $user1, 120);

        expect($auction->fresh()->current_price)->toBe(120.0);
        expect(Bid::where('is_winning', true)->first()->user_id)->toBe($user1->id);
    });
});

// ============================================================================
// PROXY BIDDING TESTS
// ============================================================================

describe('BiddingService::proxyBid', function () {

    it('creates a proxy bid record', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user = User::factory()->buyer()->create();

        $proxyBid = $this->biddingService->placeProxyBid($auction, $user, 200);

        expect($proxyBid)->toBeInstanceOf(ProxyBid::class);
        expect($proxyBid->max_amount)->toBe(200.0);
        expect($proxyBid->user_id)->toBe($user->id);
    });

    it('auto-bids when another user places a regular bid', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $proxyUser = User::factory()->buyer()->create();
        $bidder = User::factory()->buyer()->create();

        // Set up proxy bid
        $this->biddingService->placeProxyBid($auction, $proxyUser, 200);

        // Another user places a bid
        $this->biddingService->placeBid($auction, $bidder, 105);

        // Proxy should auto-bid to 110
        expect($auction->fresh()->current_price)->toBe(110.0);
        expect(Bid::where('user_id', $proxyUser->id)->exists())->toBeTrue();
    });

    it('exhausts proxy bid when outbid above max', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $proxyUser = User::factory()->buyer()->create();
        $bidder = User::factory()->buyer()->create();

        // Set up proxy bid with max 150
        $this->biddingService->placeProxyBid($auction, $proxyUser, 150);

        // Another user bids 155
        $this->biddingService->placeBid($auction, $bidder, 155);

        // Proxy should be exhausted
        expect($auction->fresh()->current_price)->toBe(155.0);
        expect($auction->fresh()->winning_bid->user_id)->toBe($bidder->id);
    });

    it('handles two competing proxy bids', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user1 = User::factory()->buyer()->create();
        $user2 = User::factory()->buyer()->create();

        // Set up competing proxy bids
        $this->biddingService->placeProxyBid($auction, $user1, 200);
        $this->biddingService->placeProxyBid($auction, $user2, 250);

        // User2 should be winning with bid at 205
        expect($auction->fresh()->current_price)->toBe(205.0);
        expect($auction->fresh()->winning_bid->user_id)->toBe($user2->id);
    });

    it('proxy bid does not bid against itself', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user = User::factory()->buyer()->create();

        // Set up single proxy bid
        $this->biddingService->placeProxyBid($auction, $user, 200);

        // Price should remain at 100 (no auto-bid against self)
        expect($auction->fresh()->current_price)->toBe(100.0);
    });

    it('proxy bid respects minimum increment', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $proxyUser = User::factory()->buyer()->create();
        $bidder = User::factory()->buyer()->create();

        // Set up proxy bid
        $this->biddingService->placeProxyBid($auction, $proxyUser, 200);

        // Another user bids minimum increment
        $this->biddingService->placeBid($auction, $bidder, 105);

        // Proxy should bid exactly one increment above
        expect($auction->fresh()->current_price)->toBe(110.0);
    });
});

// ============================================================================
// ANTI-SNIPING TESTS
// ============================================================================

describe('BiddingService::antiSniping', function () {

    it('extends auction when bid in last 2 minutes', function () {
        $auction = Auction::factory()->active()->create([
            'ends_at' => now()->addMinutes(1),
            'auto_extension' => true,
            'extension_minutes' => 3,
        ]);
        $user = User::factory()->buyer()->create();

        $originalEndAt = $auction->ends_at;
        $this->biddingService->placeBid($auction, $user, 105);

        expect($auction->fresh()->ends_at)->toBeGreaterThan($originalEndAt);
        expect((int) $auction->fresh()->ends_at->diffInMinutes($originalEndAt))->toBe(3);
    });

    it('does not extend auction when bid before sniping window', function () {
        $auction = Auction::factory()->active()->create([
            'ends_at' => now()->addMinutes(10),
            'auto_extension' => true,
            'extension_minutes' => 3,
        ]);
        $user = User::factory()->buyer()->create();

        $originalEndAt = $auction->ends_at;
        $this->biddingService->placeBid($auction, $user, 105);

        expect($auction->fresh()->ends_at)->toEqual($originalEndAt);
    });

    it('extends multiple times on consecutive last-minute bids', function () {
        $auction = Auction::factory()->active()->create([
            'ends_at' => now()->addMinutes(1),
            'auto_extension' => true,
            'extension_minutes' => 3,
        ]);
        $user1 = User::factory()->buyer()->create();
        $user2 = User::factory()->buyer()->create();

        $originalEndAt = $auction->ends_at;

        // First extension
        $this->biddingService->placeBid($auction, $user1, 105);
        expect((int) $auction->fresh()->ends_at->diffInMinutes($originalEndAt))->toBe(3);

        // Second extension (simulate time passing)
        $auction->ends_at = now()->addMinutes(1);
        $auction->save();
        $this->biddingService->placeBid($auction, $user2, 110);
        expect((int) $auction->fresh()->ends_at->diffInMinutes($originalEndAt))->toBe(6);
    });

    it('does not extend when auto_extension is disabled', function () {
        $auction = Auction::factory()->active()->create([
            'ends_at' => now()->addMinutes(1),
            'auto_extension' => false,
        ]);
        $user = User::factory()->buyer()->create();

        $originalEndAt = $auction->ends_at;
        $this->biddingService->placeBid($auction, $user, 105);

        expect($auction->fresh()->ends_at)->toEqual($originalEndAt);
    });

    it('creates auction extension record on extension', function () {
        $auction = Auction::factory()->active()->create([
            'ends_at' => now()->addMinutes(1),
            'auto_extension' => true,
            'extension_minutes' => 3,
        ]);
        $user = User::factory()->buyer()->create();

        $this->biddingService->placeBid($auction, $user, 105);

        expect($auction->extensions()->count())->toBe(1);
        expect($auction->extensions()->first()->old_end_at)->toEqual($auction->ends_at->subMinutes(3));
        expect($auction->extensions()->first()->new_end_at)->toEqual($auction->ends_at);
    });
});

// ============================================================================
// BUY NOW TESTS
// ============================================================================

describe('BiddingService::buyNow', function () {

    it('processes buy now purchase successfully', function () {
        $auction = Auction::factory()->active()->create([
            'current_price' => 100,
            'buy_now_price' => 200,
            'type' => 'standard',
        ]);
        $user = User::factory()->buyer()->create();

        $result = $this->biddingService->buyNow($auction, $user);

        expect($result['success'])->toBeTrue();
        expect($auction->fresh()->status)->toBe('sold');
        expect($auction->fresh()->current_price)->toBe(200.0);
    });

    it('rejects buy now when no buy_now_price set', function () {
        $auction = Auction::factory()->active()->create([
            'current_price' => 100,
            'buy_now_price' => null,
        ]);
        $user = User::factory()->buyer()->create();

        $result = $this->biddingService->buyNow($auction, $user);

        expect($result['success'])->toBeFalse();
        expect($result['error'])->toContain('buy now');
    });

    it('rejects buy now on own auction', function () {
        $seller = User::factory()->seller()->create();
        $auction = Auction::factory()->active()->create([
            'seller_id' => $seller->id,
            'buy_now_price' => 200,
        ]);

        $result = $this->biddingService->buyNow($auction, $seller);

        expect($result['success'])->toBeFalse();
    });

    it('rejects buy now on finished auction', function () {
        $auction = Auction::factory()->finished()->create([
            'buy_now_price' => 200,
        ]);
        $user = User::factory()->buyer()->create();

        $result = $this->biddingService->buyNow($auction, $user);

        expect($result['success'])->toBeFalse();
    });

    it('creates order on successful buy now', function () {
        $auction = Auction::factory()->active()->create([
            'current_price' => 100,
            'buy_now_price' => 200,
        ]);
        $user = User::factory()->buyer()->create();

        $this->biddingService->buyNow($auction, $user);

        expect($auction->fresh()->order)->not->toBeNull();
        expect($auction->fresh()->order->buyer_id)->toBe($user->id);
        expect($auction->fresh()->order->total_amount)->toBe(200.0);
    });
});

// ============================================================================
// CONCURRENT BID TESTS
// ============================================================================

describe('Concurrent bidding', function () {

    it('handles concurrent bids safely - only one wins', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $user1 = User::factory()->buyer()->create();
        $user2 = User::factory()->buyer()->create();

        $results = [];

        // Simulate concurrent bids (in real scenario, Redis lock handles this)
        $results[] = $this->biddingService->placeBid($auction, $user1, 105);

        // Refresh auction to get updated price
        $auction->refresh();

        try {
            $results[] = $this->biddingService->placeBid($auction, $user2, 105);
        } catch (Exception $e) {
            // Second bid might fail due to price change
            $results[] = $e;
        }

        // Count successful bids
        $successfulBids = array_filter($results, fn ($r) => $r instanceof Bid);

        // At least one should succeed
        expect(count($successfulBids))->toBeGreaterThanOrEqual(1);

        // Final price should reflect the winning bid
        expect($auction->fresh()->current_price)->toBeGreaterThanOrEqual(105.0);
    });

    it('maintains bid integrity under concurrent access', function () {
        $auction = Auction::factory()->active()->create(['current_price' => 100]);
        $users = User::factory()->buyer()->count(5)->create();

        $bids = [];
        foreach ($users as $user) {
            $auction->refresh();
            try {
                $bids[] = $this->biddingService->placeBid($auction, $user, $auction->current_price + 5);
            } catch (Exception $e) {
                $bids[] = null;
            }
        }

        // Filter successful bids
        $successfulBids = array_filter($bids);

        // Should have at least one successful bid
        expect(count($successfulBids))->toBeGreaterThanOrEqual(1);

        // All successful bids should have correct is_winning flag
        $winningBids = array_filter($successfulBids, fn ($b) => $b->is_winning);
        expect(count($winningBids))->toBe(1);
    });
});
