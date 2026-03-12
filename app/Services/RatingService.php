<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\UserRating;
use Illuminate\Support\Facades\Cache;

class RatingService
{
    public function canRate(Order $order, User $rater): bool
    {
        if ($order->status !== 'completed') {
            return false;
        }

        return ! UserRating::where('order_id', $order->id)
            ->where('rater_id', $rater->id)
            ->exists();
    }

    public function rateUser(Order $order, User $rater, int $score, ?string $comment = null): UserRating|false
    {
        if (! $this->canRate($order, $rater)) {
            return false;
        }

        if ($rater->id === $order->buyer_id) {
            $ratedUser = $order->seller;
        } else {
            $ratedUser = $order->buyer;
        }

        $rating = UserRating::create([
            'order_id' => $order->id,
            'rater_id' => $rater->id,
            'rated_id' => $ratedUser->id,
            'score' => $score,
            'comment' => $comment,
            'is_visible' => true,
        ]);

        Cache::forget("trust_score:{$ratedUser->id}");

        return $rating;
    }

    /**
     * Formula: (avg_rating × 0.6) + (transaction_bonus × 0.3) + (verification_bonus × 0.1)
     * Scaled 0-5.
     */
    public function calculateTrustScore(User $user): float
    {
        return Cache::remember("trust_score:{$user->id}", 3600, function () use ($user) {
            $ratings = UserRating::where('rated_id', $user->id)->get();

            if ($ratings->isEmpty()) {
                return 0.0;
            }

            $avgRating = (float) $ratings->avg('score');
            $completedCount = Order::where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
            })->where('status', 'completed')->count();

            $kycLevel = app(KycService::class)->getVerificationLevel($user);
            $transactionBonus = min(0.5, $completedCount * 0.05);
            $verificationBonus = min(0.5, $kycLevel * 0.15);

            return round(min(5.0, ($avgRating * 0.9) + $transactionBonus + $verificationBonus), 2);
        });
    }
}
