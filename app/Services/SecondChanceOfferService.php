<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AuctionStatus;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\SecondChanceOffer;
use App\Models\User;
use App\Notifications\AuctionWonNotification;
use Illuminate\Support\Facades\DB;

/**
 * T-1304: Second chance offer — after auction ends seller can offer to 2nd bidder.
 */
class SecondChanceOfferService
{
    /**
     * Create a second-chance offer to the next highest bidder after auction ends.
     *
     * @throws \RuntimeException
     */
    public function offer(Auction $auction, User $seller): SecondChanceOffer
    {
        $statusValue = $auction->status instanceof AuctionStatus
            ? $auction->status->value
            : (string) $auction->status;

        if (! in_array($statusValue, ['finished', 'sold'], true)) {
            throw new \RuntimeException('Second chance offer can only be made on finished auctions.');
        }

        if ($auction->seller_id !== $seller->id) {
            throw new \RuntimeException('Only the seller can create a second chance offer.');
        }

        // Find 2nd highest bidder (first losing bid by amount)
        $secondBid = Bid::query()
            ->where('auction_id', $auction->id)
            ->where('is_winning', false)
            ->whereNotNull('user_id')
            ->orderByDesc('amount')
            ->first();

        if (! $secondBid) {
            throw new \RuntimeException('No eligible second bidder found.');
        }

        // Avoid duplicate offers
        $existing = SecondChanceOffer::query()
            ->where('auction_id', $auction->id)
            ->where('buyer_id', $secondBid->user_id)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existing) {
            throw new \RuntimeException('A second chance offer already exists for this bidder.');
        }

        return DB::transaction(function () use ($auction, $seller, $secondBid) {
            $offer = SecondChanceOffer::create([
                'auction_id'    => $auction->id,
                'seller_id'     => $seller->id,
                'buyer_id'      => $secondBid->user_id,
                'offered_price' => $secondBid->amount,
                'status'        => 'pending',
                'expires_at'    => now()->addDays(2),
            ]);

            // Notify the buyer
            $secondBid->user?->notify(new AuctionWonNotification($auction, $secondBid));

            return $offer;
        });
    }

    /**
     * Accept a second-chance offer (creates an order).
     */
    public function accept(SecondChanceOffer $offer, User $buyer, AuctionService $auctionService): void
    {
        if ($offer->buyer_id !== $buyer->id || ! $offer->isPending()) {
            throw new \RuntimeException('Offer not available.');
        }

        DB::transaction(function () use ($offer, $buyer, $auctionService) {
            $offer->update(['status' => 'accepted', 'responded_at' => now()]);
            $auctionService->createOrder($offer->auction, $buyer);
        });
    }

    public function decline(SecondChanceOffer $offer, User $buyer): void
    {
        if ($offer->buyer_id !== $buyer->id || ! $offer->isPending()) {
            throw new \RuntimeException('Offer not available.');
        }

        $offer->update(['status' => 'declined', 'responded_at' => now()]);
    }
}
