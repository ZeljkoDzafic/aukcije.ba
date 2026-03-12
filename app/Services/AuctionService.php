<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AuctionStatus;
use App\Events\AuctionEnded;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AuctionService
{
    protected BiddingService $biddingService;

    public function __construct(?BiddingService $biddingService = null)
    {
        $this->biddingService = $biddingService ?? app(BiddingService::class);
    }

    /**
     * Create a new auction in draft status.
     *
     * @param array<string, mixed> $data
     * @throws \RuntimeException when the seller has reached their tier auction limit
     */
    public function createAuction(User $seller, array $data): Auction
    {
        return DB::transaction(function () use ($seller, $data) {
            $tier = $seller->getTier();

            if ($tier['auction_limit'] >= 0) {
                $activeCount = $seller->auctions()->where('status', AuctionStatus::Active->value)->count();
                if ($activeCount >= $tier['auction_limit']) {
                    throw new \RuntimeException('Dostignut je limit aktivnih aukcija za vaš tier.');
                }
            }

            $payload = [
                'seller_id' => $seller->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'start_price' => $data['start_price'],
                'current_price' => $data['start_price'],
                'buy_now_price' => $data['buy_now_price'] ?? null,
                'reserve_price' => $data['reserve_price'] ?? null,
                'type' => $data['type'] ?? 'standard',
                'condition' => in_array($data['condition'] ?? 'used', ['new', 'like_new', 'used', 'for_parts'], true) ? ($data['condition'] ?? 'used') : 'used',
                'ends_at' => now()->addDays($data['duration_days'] ?? 7),
                'starts_at' => now(),
                'auto_extension' => $data['auto_extension'] ?? true,
                'extension_minutes' => config('auction.extension_minutes', 3),
                'status' => AuctionStatus::Draft->value,
                'bids_count' => 0,
            ];

            if (! empty($payload['category_id'])) {
                $categoryExists = Category::query()->whereKey((string) $payload['category_id'])->exists();

                if (! $categoryExists) {
                    $payload['category_id'] = null;
                }
            }

            $filteredPayload = [];

            foreach ($payload as $column => $value) {
                if (Schema::hasColumn('auctions', $column)) {
                    $filteredPayload[$column] = $value;
                }
            }

            return Auction::create($filteredPayload);
        });
    }

    /**
     * Transition an auction from draft to active.
     */
    public function publishAuction(Auction $auction): Auction
    {
        return DB::transaction(function () use ($auction) {
            $auction->update([
                'status' => AuctionStatus::Active->value,
                'starts_at' => $auction->starts_at ?? now(),
            ]);

            return $auction->fresh();
        });
    }

    /**
     * Finalise an auction: determine winner, create order, fire events, notify watchers.
     */
    public function endAuction(Auction $auction): void
    {
        if ($this->statusValue($auction) !== AuctionStatus::Active->value) {
            return;
        }

        DB::transaction(function () use ($auction) {
            /** @var Bid|null $winningBid */
            $winningBid = $auction->bids()->where('is_winning', true)->with('user')->first();

            if (! $winningBid) {
                // No bids at all
                $auction->update([
                    'status' => AuctionStatus::Finished->value,
                    'ended_at' => now(),
                ]);

                AuctionEnded::dispatch($auction->fresh(), null);

                return;
            }

            $reserveMet = $auction->reserve_price === null
                || $winningBid->amount >= $auction->reserve_price;

            if (! $reserveMet) {
                // Reserve price not met
                $auction->update([
                    'status' => AuctionStatus::Finished->value,
                    'ended_at' => now(),
                    'winner_id' => null,
                ]);

                Log::info("Auction {$auction->id} ended without meeting reserve price.");
                AuctionEnded::dispatch($auction->fresh(), null);

                return;
            }

            // Successful sale
            $auction->update([
                'status' => AuctionStatus::Sold->value,
                'winner_id' => $winningBid->user_id,
                'ended_at' => now(),
            ]);

            $this->createOrder($auction, $winningBid->user);

            AuctionEnded::dispatch($auction->fresh(), $winningBid);
        });
    }

    /**
     * Cancel an auction — only allowed when no bids have been placed.
     *
     * @throws \RuntimeException when the auction already has bids
     */
    public function cancelAuction(Auction $auction): void
    {
        if ($auction->bids_count > 0) {
            throw new \RuntimeException('Ne možeš otkazati aukciju koja već ima ponude.');
        }

        $auction->update([
            'status' => AuctionStatus::Cancelled->value,
        ]);
    }

    /**
     * Create an Order record for a completed auction.
     */
    public function createOrder(Auction $auction, User $buyer): Order
    {
        $commissionRate = $auction->seller->getCommissionRate();

        $payload = [
            'auction_id' => $auction->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $auction->seller_id,
            'total_amount' => $auction->current_price,
            'commission_amount' => $auction->current_price * $commissionRate,
            'seller_payout' => $auction->current_price * (1 - $commissionRate),
            'status' => 'pending_payment',
            'payment_status' => 'pending',
            'payment_deadline_at' => now()->addDays(config('escrow.payment_deadline_days', 3)),
        ];

        if (Schema::hasColumn('orders', 'amount')) {
            $payload['amount'] = $auction->current_price;
        }

        if (Schema::hasColumn('orders', 'commission')) {
            $payload['commission'] = $auction->current_price * $commissionRate;
        }

        $filteredPayload = [];

        foreach ($payload as $column => $value) {
            if (Schema::hasColumn('orders', $column)) {
                $filteredPayload[$column] = $value;
            }
        }

        return Order::create($filteredPayload);
    }

    /**
     * Delegate anti-sniping check to BiddingService.
     */
    public function checkAntiSniping(Auction $auction, Bid $bid): void
    {
        $this->biddingService->checkAntiSniping($auction, $bid);
    }

    /**
     * Check if an auction can transition to a given status.
     */
    public function canTransitionTo(Auction $auction, string $newStatus): bool
    {
        $transitions = config('auction.state_transitions', [
            'draft' => ['active', 'cancelled'],
            'active' => ['finished', 'sold', 'cancelled'],
            'finished' => ['sold'],
            'sold' => [],
            'cancelled' => [],
        ]);

        return in_array($newStatus, $transitions[$this->statusValue($auction)] ?? [], true);
    }

    protected function statusValue(Auction $auction): string
    {
        return $auction->status instanceof AuctionStatus
            ? $auction->status->value
            : (string) $auction->status;
    }
}
