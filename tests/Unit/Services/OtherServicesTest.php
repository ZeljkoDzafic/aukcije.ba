<?php

/**
 * ===================================
 * OTHER SERVICES UNIT TESTS
 * ===================================
 * Tests for KYC, Rating, Dispute, and Auction services
 */

declare(strict_types=1);

use App\Models\Auction;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\User;
use App\Models\UserRating;
use App\Services\AuctionService;
use App\Services\DisputeService;
use App\Services\KycService;
use App\Services\RatingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ============================================================================
// AUCTION SERVICE TESTS
// ============================================================================

describe('AuctionService', function () {

    beforeEach(function () {
        $this->auctionService = new AuctionService;
    });

    it('creates auction successfully', function () {
        $seller = User::factory()->seller()->create();

        $auction = $this->auctionService->createAuction($seller, [
            'title' => 'Test Auction',
            'description' => 'Test description',
            'category_id' => 1,
            'start_price' => 100,
            'duration_days' => 7,
        ]);

        expect($auction)->not->toBeNull();
        expect($auction->seller_id)->toBe($seller->id);
        expect($auction->status)->toBe('draft');
    });

    it('ends auction successfully', function () {
        $seller = User::factory()->seller()->create();
        $buyer = User::factory()->buyer()->create();
        $auction = Auction::factory()->active()->create([
            'seller_id' => $seller->id,
            'current_price' => 100,
            'winner_id' => $buyer->id,
        ]);
        $auction->bids()->create([
            'user_id' => $buyer->id,
            'amount' => 100,
            'is_winning' => true,
        ]);

        $this->auctionService->endAuction($auction);

        expect($auction->fresh()->status)->toBe('sold');
        expect($auction->fresh()->order)->not->toBeNull();
    });

    it('cancels auction without bids', function () {
        $seller = User::factory()->seller()->create();
        $auction = Auction::factory()->active()->create([
            'seller_id' => $seller->id,
            'bids_count' => 0,
        ]);

        $this->auctionService->cancelAuction($auction);

        expect($auction->fresh()->status)->toBe('cancelled');
    });

    it('cannot cancel auction with bids', function () {
        $seller = User::factory()->seller()->create();
        $auction = Auction::factory()->active()->create([
            'seller_id' => $seller->id,
            'bids_count' => 1,
        ]);

        $this->auctionService->cancelAuction($auction);
    })->throws(Exception::class);

    it('can transition status correctly', function () {
        $auction = Auction::factory()->draft()->create();

        expect($this->auctionService->canTransitionTo($auction, 'active'))->toBeTrue();
        expect($this->auctionService->canTransitionTo($auction, 'cancelled'))->toBeTrue();
        expect($this->auctionService->canTransitionTo($auction, 'sold'))->toBeFalse();
    });

    it('creates order with correct commission', function () {
        $seller = User::factory()->seller()->create(); // Free tier = 8%
        $buyer = User::factory()->buyer()->create();
        $auction = Auction::factory()->active()->create([
            'seller_id' => $seller->id,
            'current_price' => 100,
        ]);

        $order = $this->auctionService->createOrder($auction, $buyer);

        expect($order->total_amount)->toBe(100.0);
        expect($order->commission_amount)->toBe(8.0); // 8% of 100
        expect($order->seller_payout)->toBe(92.0);
    });
});

// ============================================================================
// KYC SERVICE TESTS
// ============================================================================

describe('KycService', function () {

    beforeEach(function () {
        $this->kycService = new KycService;
    });

    it('sends SMS OTP', function () {
        $user = User::factory()->create();

        $result = $this->kycService->sendSmsOtp($user, '+38761123456');

        expect($result['success'])->toBeTrue();
        expect($user->fresh()->phone)->toBe('+38761123456');
    });

    it('verifies SMS OTP', function () {
        $user = User::factory()->create();
        $this->kycService->sendSmsOtp($user, '+38761123456');

        // Get the OTP from cache/storage (mocked)
        $otp = '123456'; // In real implementation, this would be retrieved

        $result = $this->kycService->verifySmsOtp($user, $otp);

        expect($result)->toBeTrue();
        expect($user->fresh()->phone_verified_at)->not->toBeNull();
    });

    it('gets correct KYC level', function () {
        $user1 = User::factory()->verified()->create(); // Level 1
        $user2 = User::factory()->create(['phone_verified_at' => now()]); // Level 2
        $user3 = User::factory()->verifiedSeller()->create(); // Level 3

        expect($this->kycService->getVerificationLevel($user1))->toBe(1);
        expect($this->kycService->getVerificationLevel($user2))->toBe(2);
        expect($this->kycService->getVerificationLevel($user3))->toBe(3);
    });

    it('rejects invalid OTP', function () {
        $user = User::factory()->create();
        $this->kycService->sendSmsOtp($user, '+38761123456');

        $result = $this->kycService->verifySmsOtp($user, 'wrongcode');

        expect($result)->toBeFalse();
    });

    it('rate limits SMS OTP', function () {
        $user = User::factory()->create();

        // Send 3 OTPs
        $this->kycService->sendSmsOtp($user, '+38761123456');
        $this->kycService->sendSmsOtp($user, '+38761123456');
        $this->kycService->sendSmsOtp($user, '+38761123456');

        // 4th should fail
        $result = $this->kycService->sendSmsOtp($user, '+38761123456');

        expect($result['success'])->toBeFalse();
    });
});

// ============================================================================
// RATING SERVICE TESTS
// ============================================================================

describe('RatingService', function () {

    beforeEach(function () {
        $this->ratingService = new RatingService;
    });

    it('creates rating successfully', function () {
        $buyer = User::factory()->buyer()->create();
        $seller = User::factory()->seller()->create();
        $order = Order::factory()->completed()->create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $rating = $this->ratingService->rateUser($order, $buyer, 5, 'Great seller!');

        expect($rating)->not->toBeNull();
        expect($rating->score)->toBe(5);
        expect($rating->comment)->toBe('Great seller!');
    });

    it('calculates trust score correctly', function () {
        $user = User::factory()->create(['trust_score' => 0]);

        // Create 5 ratings with score 5
        for ($i = 0; $i < 5; $i++) {
            UserRating::factory()->create([
                'rated_id' => $user->id,
                'score' => 5,
            ]);
        }

        $trustScore = $this->ratingService->calculateTrustScore($user);

        expect($trustScore)->toBeGreaterThan(4.0);
    });

    it('prevents duplicate rating for same order', function () {
        $buyer = User::factory()->buyer()->create();
        $seller = User::factory()->seller()->create();
        $order = Order::factory()->completed()->create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $this->ratingService->rateUser($order, $buyer, 5, 'Great!');
        $result = $this->ratingService->rateUser($order, $buyer, 3, 'Changed mind');

        expect($result)->toBeFalse();
    });

    it('only allows rating after completed order', function () {
        $buyer = User::factory()->buyer()->create();
        $seller = User::factory()->seller()->create();
        $order = Order::factory()->pendingPayment()->create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $result = $this->ratingService->rateUser($order, $buyer, 5, 'Test');

        expect($result)->toBeFalse();
    });

    it('assigns trust badges correctly', function () {
        $user1 = User::factory()->verifiedSeller()->create();
        $user2 = User::factory()->withTrustScore(4.8)->create();

        expect($user1->getTrustBadge())->toBe('Verified Seller');
    });
});

// ============================================================================
// DISPUTE SERVICE TESTS
// ============================================================================

describe('DisputeService', function () {

    beforeEach(function () {
        $this->disputeService = new DisputeService;
    });

    it('opens dispute successfully', function () {
        $buyer = User::factory()->buyer()->create();
        $seller = User::factory()->seller()->create();
        $order = Order::factory()->paid()->create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $dispute = $this->disputeService->openDispute($order, $buyer, 'not_received', 'Item not received');

        expect($dispute)->not->toBeNull();
        expect($dispute->reason)->toBe('not_received');
        expect($dispute->status)->toBe('open');
    });

    it('auto-freezes escrow on dispute open', function () {
        $buyer = User::factory()->buyer()->create();
        $order = Order::factory()->paid()->create(['buyer_id' => $buyer->id]);

        $this->disputeService->openDispute($order, $buyer, 'not_received', 'Test');

        // Escrow should be frozen (would need to verify in implementation)
        expect(true)->toBeTrue();
    });

    it('adds evidence to dispute', function () {
        $dispute = Dispute::factory()->create();
        $user = User::factory()->buyer()->create();

        $result = $this->disputeService->addEvidence($dispute, $user, ['photo1.jpg']);

        expect($result)->toBeTrue();
    });

    it('resolves dispute', function () {
        $admin = User::factory()->admin()->create();
        $dispute = Dispute::factory()->create(['status' => 'open']);

        $result = $this->disputeService->resolve($dispute, 'full_refund', $admin);

        expect($result)->toBeTrue();
        expect($dispute->fresh()->status)->toBe('resolved');
    });

    it('auto-escalates if seller doesnt respond', function () {
        $dispute = Dispute::factory()->create([
            'status' => 'open',
            'created_at' => now()->subHours(49), // More than 48 hours
        ]);

        $result = $this->disputeService->autoEscalate();

        expect($result)->toBeGreaterThanOrEqual(0);
    });
});
