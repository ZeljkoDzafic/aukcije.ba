<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AuctionStatus;
use App\Events\AuctionExtended;
use App\Events\BidPlaced;
use App\Exceptions\AuctionNotActiveException;
use App\Exceptions\BidTooLowException;
use App\Exceptions\CannotBidOwnAuctionException;
use App\Models\Auction;
use App\Models\AuctionExtension;
use App\Models\Bid;
use App\Models\ProxyBid;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BiddingService
{
    protected BidIncrementService $incrementService;

    public function __construct(?BidIncrementService $incrementService = null)
    {
        $this->incrementService = $incrementService ?? app(BidIncrementService::class);
    }

    /**
     * Place a bid on an auction.
     *
     * @throws AuctionNotActiveException
     * @throws CannotBidOwnAuctionException
     * @throws BidTooLowException
     */
    public function placeBid(
        User|Auction $user,
        Auction|User $auction,
        float $amount,
        bool $isProxy = false,
        ?float $maxProxyAmount = null,
    ): Bid {
        if ($user instanceof Auction && $auction instanceof User) {
            [$user, $auction] = [$auction, $user];
        }

        $lock = Cache::lock(
            "auction_bid:{$auction->id}",
            config('auction.lock_timeout_seconds', 10),
        );

        return $lock->block(5, fn () => $this->placeBidWithinLock($user, $auction, $amount, $isProxy, $maxProxyAmount));
    }

    protected function placeBidWithinLock(
        User $user,
        Auction $auction,
        float $amount,
        bool $isProxy = false,
        ?float $maxProxyAmount = null,
    ): Bid {
        $auction->refresh();

        $this->validateBid($user, $auction, $amount);

        return DB::transaction(function () use ($user, $auction, $amount, $isProxy, $maxProxyAmount) {
            $auction->bids()->where('is_winning', true)->update(['is_winning' => false]);

            $bid = Bid::create([
                'auction_id' => $auction->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'is_proxy' => $isProxy,
                'is_winning' => true,
            ]);

            if ($isProxy && $maxProxyAmount !== null) {
                ProxyBid::updateOrCreate(
                    ['auction_id' => $auction->id, 'user_id' => $user->id],
                    ['max_amount' => $maxProxyAmount, 'is_active' => true],
                );
            }

            $auction->update([
                'current_price' => $amount,
                'bids_count' => $auction->bids_count + 1,
                'last_bid_at' => now(),
                'winner_id' => $user->id,
            ]);

            $this->checkAntiSniping($auction, $bid);
            $this->processProxyBids($auction, $bid);

            $freshBid = $bid->fresh();
            BidPlaced::dispatch($auction->fresh(), $freshBid);

            return $freshBid;
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
        if ($user->is_banned) {
            throw new \RuntimeException('User is banned');
        }

        if (! $user->email_verified_at) {
            throw new \RuntimeException('User email is not verified');
        }

        $status = $auction->status instanceof AuctionStatus
            ? $auction->status->value
            : (string) $auction->status;

        if ($status !== AuctionStatus::Active->value || now()->gt($auction->ends_at)) {
            throw new AuctionNotActiveException('Aukcija nije aktivna.');
        }

        if ($auction->seller_id === $user->id) {
            throw new CannotBidOwnAuctionException;
        }

        $minimum = $this->incrementService->getMinimumBid($auction->current_price);
        if ($amount < $minimum) {
            throw new BidTooLowException($minimum, $amount);
        }
    }

    public function placeProxyBid(Auction $auction, User $user, float $maxAmount): ProxyBid
    {
        $this->validateBid($user, $auction, max($auction->minimum_bid, $auction->current_price + 0.01));

        $proxyBid = ProxyBid::updateOrCreate(
            ['auction_id' => $auction->id, 'user_id' => $user->id],
            ['max_amount' => $maxAmount, 'is_active' => true],
        );

        $activeProxies = ProxyBid::query()
            ->where('auction_id', $auction->id)
            ->where('is_active', true)
            ->orderByDesc('max_amount')
            ->get();

        if ($activeProxies->count() >= 2) {
            $highest = $activeProxies[0];
            $secondHighest = $activeProxies[1];
            $increment = $this->incrementService->getIncrement((float) $auction->current_price);
            $targetAmount = min((float) $highest->max_amount, (float) $secondHighest->max_amount + $increment);

            if ((float) $auction->current_price < $targetAmount) {
                $this->placeBid($highest->user, $auction, $targetAmount, true, (float) $highest->max_amount);
            }
        }

        return $proxyBid->fresh();
    }

    /**
     * @return array{success: bool, error?: string, order_id?: string}
     */
    public function buyNow(Auction $auction, User $user): array
    {
        if (! $auction->buy_now_price) {
            return ['success' => false, 'error' => 'Auction does not support buy now'];
        }

        if ($auction->seller_id === $user->id) {
            return ['success' => false, 'error' => 'Cannot buy your own auction'];
        }

        $status = $auction->status instanceof AuctionStatus
            ? $auction->status->value
            : (string) $auction->status;

        if ($status !== AuctionStatus::Active->value || now()->gt($auction->ends_at)) {
            return ['success' => false, 'error' => 'Auction is not active'];
        }

        $auction->update([
            'current_price' => $auction->buy_now_price,
            'winner_id' => $user->id,
            'status' => AuctionStatus::Sold->value,
            'ended_at' => now(),
        ]);

        $order = (new AuctionService($this))->createOrder($auction->fresh(), $user);

        return [
            'success' => true,
            'order_id' => $order->id,
        ];
    }

    /**
     * Process competing proxy bids after a new bid is placed.
     * Limited to 50 iterations to prevent infinite loops.
     */
    public function processProxyBids(Auction $auction, Bid $winningBid): void
    {
        $maxIterations = 50;
        $iteration = 0;

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
                $newBid = $this->placeBidWithinLock(
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

                $newBid = $this->placeBidWithinLock(
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
        $currentEndsAt = $auction->ends_at->copy();

        if ($bid->created_at >= $currentEndsAt->copy()->subSeconds($snipingWindow)) {
            $extensionMinutes = (int) ($auction->extension_minutes ?: config('auction.extension_minutes', 3));
            $baseEndAt = $auction->original_end_at?->copy() ?? $currentEndsAt->copy();
            $extensionCount = $auction->extensions()->count() + 1;
            $newEndsAt = $baseEndAt->copy()->addMinutes($extensionMinutes * $extensionCount);

            AuctionExtension::create([
                'auction_id' => $auction->id,
                'triggered_by_bid_id' => $bid->id,
                'old_end_at' => $currentEndsAt,
                'new_end_at' => $newEndsAt,
                'extension_minutes' => $extensionMinutes,
            ]);

            $auction->update([
                'original_end_at' => $auction->original_end_at ?? $currentEndsAt,
                'ends_at' => $newEndsAt,
            ]);

            AuctionExtended::dispatch($auction->fresh(), $newEndsAt);
        }
    }
}
