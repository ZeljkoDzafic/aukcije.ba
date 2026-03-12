<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AdminLog;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * T-1100: Risk scoring for users, auctions, and bid actions.
 * Score range: 0 (clean) → 100 (high risk).
 */
class FraudScoringService
{
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Calculate and persist a user's fraud risk score.
     * Factors: account age, bid velocity, win/bid ratio, reports, KYC level.
     */
    public function calculateScore(User $user): int
    {
        $score = 0;

        // Account age penalty — new accounts are higher risk
        $ageInDays = (int) $user->created_at->diffInDays(now());
        if ($ageInDays < 1) {
            $score += 30;
        } elseif ($ageInDays < 7) {
            $score += 20;
        } elseif ($ageInDays < 30) {
            $score += 10;
        }

        // Bid velocity — too many bids in 24h is suspicious
        $bidsLast24h = Bid::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        if ($bidsLast24h > 100) {
            $score += 25;
        } elseif ($bidsLast24h > 50) {
            $score += 15;
        } elseif ($bidsLast24h > 20) {
            $score += 5;
        }

        // Win/bid ratio — never winning is suspicious (possible shill)
        $totalBids = Bid::query()->where('user_id', $user->id)->count();
        $totalWins = DB::table('orders')->where('buyer_id', $user->id)->count();

        if ($totalBids > 20 && $totalWins === 0) {
            $score += 20;
        }

        // KYC level bonus — verified users are lower risk
        $kycLevel = (int) ($user->kyc_level ?? 0);
        $score -= min(20, $kycLevel * 7);

        // Existing bans or reports increase score
        if ($user->is_banned) {
            $score += 40;
        }

        $score = max(0, min(100, $score));

        // Persist to DB and cache
        $user->updateQuietly(['fraud_score' => $score]);
        Cache::put("fraud_score:{$user->id}", $score, self::CACHE_TTL);

        return $score;
    }

    /**
     * Get cached score (recalculates if stale).
     */
    public function getScore(User $user): int
    {
        return Cache::remember("fraud_score:{$user->id}", self::CACHE_TTL, fn () => $this->calculateScore($user));
    }

    /**
     * Evaluate risk for a specific bid action and log if suspicious.
     * Returns array with ['score', 'flags', 'should_flag'].
     *
     * @return array{score: int, flags: string[], should_flag: bool}
     */
    public function evaluateBidRisk(User $user, Auction $auction): array
    {
        $score = $this->getScore($user);
        $flags = [];

        // Check if bidding on own-seller's auction via different account
        $sellerBidCount = Bid::query()
            ->where('auction_id', $auction->id)
            ->where('user_id', $auction->seller_id)
            ->count();

        if ($sellerBidCount > 0) {
            $flags[] = 'seller_bid_own_auction';
            $score += 50;
        }

        // Rapid back-and-forth bidding pattern
        $recentBidsOnAuction = Bid::query()
            ->where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();

        if ($recentBidsOnAuction > 5) {
            $flags[] = 'rapid_repeated_bids';
            $score += 15;
        }

        $score = min(100, $score);
        $shouldFlag = $score >= 60 || ! empty($flags);

        if ($shouldFlag) {
            $this->logRiskFlag($user, $auction, $score, $flags);
        }

        return [
            'score'       => $score,
            'flags'       => $flags,
            'should_flag' => $shouldFlag,
        ];
    }

    private function logRiskFlag(User $user, Auction $auction, int $score, array $flags): void
    {
        AdminLog::create([
            'admin_id'    => null,
            'action'      => 'fraud_flag',
            'target_type' => 'user',
            'target_id'   => $user->id,
            'metadata'    => [
                'auction_id'   => $auction->id,
                'risk_score'   => $score,
                'risk_signals' => $flags,
                'review_status' => 'pending',
                'risk_level'   => $score >= 80 ? 'high' : ($score >= 60 ? 'medium' : 'low'),
            ],
        ]);
    }
}
