<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\AuctionExtension;
use App\Models\Bid;
use App\Models\ProxyBid;
use App\Models\User;
use App\Enums\AuctionStatus;
use App\Events\BidPlaced;
use App\Events\AuctionExtended;
use App\Exceptions\BidTooLowException;
use App\Exceptions\AuctionNotActiveException;
use App\Exceptions\CannotBidOwnAuctionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BiddingService
{
    public function __construct(
        protected BidIncrementService $incrementService,
    ) {}

    /**
     * Place a bid on an auction.
     *
     * @throws AuctionNotActiveException
     * @throws CannotBidOwnAuctionException
     * @throws BidTooLowException
     */
    public function placeBid(
        User $user,
        Auction $auction,
        float $amount,
        bool $isProxy = false,
        ?float $maxProxyAmount = null,
    ): Bid {
        $lock = Cache::lock(
            "auction_bid:{$auction->id}",
            config('auction.lock_timeout_seconds', 10),
        );

        return $lock->block(5, function () use ($user, $auction, $amount, $isProxy, $maxProxyAmount) {
            // Refresh to get latest state inside the lock
            $auction->refresh();

            $this->validateBid($user, $auction, $amount);

            return DB::transaction(function () use ($user, $auction, $amount, $isProxy, $maxProxyAmount) {
                // Mark previous winning bid as not winning
                $auction->bids()->where('is_winning', true)->update(['is_winning' => false]);

                // Create the bid record
                $bid = Bid::create([
                    'auction_id' => $auction->id,
                    'user_id'    => $user->id,
                    'amount'     => $amount,
                    'is_proxy'   => $isProxy,
                    'is_winning' => true,
                ]);

                // Create or update the proxy bid record when applicable
                if ($isProxy && $maxProxyAmount !== null) {
                    ProxyBid::updateOrCreate(
                        ['auction_id' => $auction->id, 'user_id' => $user->id],
                        ['max_amount' => $maxProxyAmount, 'is_active' => true],
                    );
                }

                // Update auction counters and current price
                $auction->update([
                    'current_price' => $amount,
                    'bids_count'    => $auction->bids_count + 1,
                    'last_bid_at'   => now(),
                    'winner_id'     => $user->id,
                ]);

                // Anti-sniping check
                $this->checkAntiSniping($auction, $bid);

                // Compete against existing proxy bids
                $this->processProxyBids($auction, $bid);

                BidPlaced::dispatch($auction->fresh(), $bid);

                return $bid;
            });
        });
    }

    /**
     * Validate that a bid is legal before persisting it.
     *
     * @throws AuctionNotActiveException
     * @throws CannotBidOwnAuctionException
     * @throws BidTooLowException
     */
    public function validateBid(User $user, Auction $auction, float $amount): void
    {
        if (
            $auction->status !== AuctionStatus::Active->value
            || now()->gt($auction->ends_at)
        ) {
            throw new AuctionNotActiveException('Aukcija nije aktivna.');
        }

        if ($auction->seller_id === $user->id) {
            throw new CannotBidOwnAuctionException('Ne možeš licitirati na vlastitoj aukciji.');
        }

        $minimum = $this->incrementService->getMinimumBid($auction->current_price);
        if ($amount < $minimum) {
            throw new BidTooLowException($minimum, $amount);
        }
    }

    /**
     * Process competing proxy bids after a new bid is placed.
     * Limited to 50 iterations to prevent infinite loops.
     */
    public function processProxyBids(Auction $auction, Bid $winningBid): void
    {
        $maxIterations = 50;
        $iteration     = 0;

        while ($iteration < $maxIterations) {
            $iteration++;

            $auction->refresh();

            // Highest competing proxy bid (excluding current winner's user)
            $competitorProxy = ProxyBid::where('auction_id', $auction->id)
                ->where('user_id', '!=', $winningBid->user_id)
                ->where('is_active', true)
                ->orderByDesc('max_amount')
                ->first();

            if (! $competitorProxy) {
                break;
            }

            if ($competitorProxy->max_amount <= $auction->current_price) {
                // Competitor cannot outbid — deactivate their proxy
                $competitorProxy->update(['is_active' => false]);
                break;
            }

            // Determine how much the current winner's proxy can auto-respond with
            $winnerProxy = ProxyBid::where('auction_id', $auction->id)
                ->where('user_id', $winningBid->user_id)
                ->where('is_active', true)
                ->first();

            $increment = $this->incrementService->getIncrement($auction->current_price);

            if ($winnerProxy && $winnerProxy->max_amount > $auction->current_price) {
                // Current winner can respond: bid just enough to stay ahead, capped at their max
                $counterAmount = min(
                    $competitorProxy->max_amount + $increment,
                    $winnerProxy->max_amount,
                );

                if ($counterAmount <= $auction->current_price) {
                    break;
                }

                // Auto-place on behalf of the current winner
                $newBid = $this->placeBid(
                    $winnerProxy->user,
                    $auction,
                    $counterAmount,
                    true,
                    $winnerProxy->max_amount,
                );

                $winningBid = $newBid;
            } else {
                // Current winner has no proxy or exhausted it — competitor wins
                $competitorAmount = min(
                    $competitorProxy->max_amount,
                    $auction->current_price + $increment,
                );

                $newBid = $this->placeBid(
                    $competitorProxy->user,
                    $auction,
                    $competitorAmount,
                    true,
                    $competitorProxy->max_amount,
                );

                $winningBid = $newBid;
                break; // No further proxy chain possible
            }
        }

        if ($iteration >= $maxIterations) {
            Log::warning("processProxyBids reached max iterations for auction {$auction->id}");
        }
    }

    /**
     * Extend the auction end time if a bid lands inside the sniping window.
     */
    public function checkAntiSniping(Auction $auction, Bid $bid): void
    {
        if (! $auction->auto_extension) {
            return;
        }

        $snipingWindow = config('auction.sniping_window', 120); // seconds

        if ($bid->created_at >= $auction->ends_at->subSeconds($snipingWindow)) {
            $extensionMinutes = config('auction.extension_minutes', 3);
            $newEndsAt        = $auction->ends_at->addMinutes($extensionMinutes);

            AuctionExtension::create([
                'auction_id'          => $auction->id,
                'triggered_by_bid_id' => $bid->id,
                'old_end_at'          => $auction->ends_at,
                'new_end_at'          => $newEndsAt,
                'extension_minutes'   => $extensionMinutes,
            ]);

            $auction->update(['ends_at' => $newEndsAt]);

            AuctionExtended::dispatch($auction->fresh(), $newEndsAt);
        }
    }
}
