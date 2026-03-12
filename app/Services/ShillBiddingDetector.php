<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AdminLog;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * T-1101: Shill bidding detection heuristics.
 *
 * Shill bidding = seller or seller's associates artificially inflate prices by bidding.
 * Detection signals: shared IP, same device fingerprint, relationship to seller,
 * never-winning bid pattern, bid-then-withdraw pattern.
 */
class ShillBiddingDetector
{
    /**
     * Analyse a completed/active auction for shill bidding signals.
     * Returns flagged bidders with their signals.
     *
     * @return array{flagged: bool, suspects: array<string, array{user_id: string, signals: string[], confidence: string}>}
     */
    public function analyseAuction(Auction $auction): array
    {
        $bids = Bid::query()
            ->where('auction_id', $auction->id)
            ->with('user')
            ->orderBy('created_at')
            ->get();

        if ($bids->isEmpty()) {
            return ['flagged' => false, 'suspects' => []];
        }

        $suspects = [];

        foreach ($bids->groupBy('user_id') as $userId => $userBids) {
            if ($userId === $auction->seller_id) {
                continue; // direct seller bid caught elsewhere
            }

            $signals = $this->detectSignalsForUser($userId, $userBids, $auction, $bids);

            if (! empty($signals)) {
                $confidence = count($signals) >= 3 ? 'high' : (count($signals) >= 2 ? 'medium' : 'low');

                $suspects[$userId] = [
                    'user_id'    => $userId,
                    'signals'    => $signals,
                    'confidence' => $confidence,
                ];
            }
        }

        $flagged = ! empty($suspects);

        if ($flagged) {
            $this->recordFinding($auction, $suspects);
        }

        return ['flagged' => $flagged, 'suspects' => $suspects];
    }

    /**
     * @param Collection<int, Bid> $userBids
     * @param Collection<int, Bid> $allBids
     * @return string[]
     */
    private function detectSignalsForUser(
        string $userId,
        Collection $userBids,
        Auction $auction,
        Collection $allBids
    ): array {
        $signals = [];

        // Signal 1: User bids but never wins (across all auctions > 10 bids)
        $totalBidsEver = Bid::query()->where('user_id', $userId)->count();
        $winsEver = DB::table('orders')->where('buyer_id', $userId)->count();

        if ($totalBidsEver > 10 && $winsEver === 0) {
            $signals[] = 'never_wins';
        }

        // Signal 2: Bidding pattern — only outbid by one other user consistently
        $outbidByUsers = [];
        foreach ($userBids as $bid) {
            $nextBid = $allBids->first(fn (Bid $b) => $b->amount > $bid->amount && $b->created_at->gt($bid->created_at));
            if ($nextBid) {
                $outbidByUsers[] = $nextBid->user_id;
            }
        }

        $dominantOutbidder = collect($outbidByUsers)->countBy()->sortDesc()->keys()->first();
        if ($dominantOutbidder === $auction->seller_id) {
            $signals[] = 'always_outbid_by_seller_account';
        }

        // Signal 3: Bids in rapid succession from same user (< 30 seconds apart)
        $previousTime = null;
        $rapidCount = 0;
        foreach ($userBids->sortBy('created_at') as $bid) {
            if ($previousTime && $bid->created_at->diffInSeconds($previousTime) < 30) {
                $rapidCount++;
            }
            $previousTime = $bid->created_at;
        }

        if ($rapidCount >= 3) {
            $signals[] = 'rapid_succession_bids';
        }

        // Signal 4: User is related to seller (same seller_id in past orders as seller)
        $hasSellerRelationship = DB::table('orders')
            ->where('seller_id', $userId)
            ->where('buyer_id', $auction->seller_id)
            ->exists();

        if ($hasSellerRelationship) {
            $signals[] = 'seller_relationship';
        }

        return $signals;
    }

    /**
     * @param array<string, array{user_id: string, signals: string[], confidence: string}> $suspects
     */
    private function recordFinding(Auction $auction, array $suspects): void
    {
        AdminLog::create([
            'admin_id'    => null,
            'action'      => 'shill_bidding_detected',
            'target_type' => 'auction',
            'target_id'   => $auction->id,
            'metadata'    => [
                'seller_id'     => $auction->seller_id,
                'suspects'      => array_values($suspects),
                'review_status' => 'pending',
                'risk_level'    => collect($suspects)->contains('confidence', 'high') ? 'high' : 'medium',
                'risk_signals'  => array_unique(array_merge(...array_column(array_values($suspects), 'signals'))),
            ],
        ]);
    }
}
