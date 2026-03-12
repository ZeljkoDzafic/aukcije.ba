<?php
namespace App\Services;

use App\Models\{Order, User, UserRating};
use Illuminate\Support\Facades\Cache;

class RatingService
{
    public function canRate(Order $order, User $rater): bool
    {
        if ($order->status !== 'completed') return false;

        return !UserRating::where('order_id', $order->id)
            ->where('rater_id', $rater->id)
            ->exists();
    }

    public function rateUser(Order $order, User $rater, int $score, ?string $comment = null): UserRating
    {
        if (!$this->canRate($order, $rater)) {
            throw new \RuntimeException('Ne možete ocjenjivati ovu narudžbu.');
        }

        // Determine who is being rated and the type
        if ($rater->id === $order->buyer_id) {
            $ratedUser = $order->seller;
            $type = 'seller';
        } else {
            $ratedUser = $order->buyer;
            $type = 'buyer';
        }

        $rating = UserRating::create([
            'order_id'       => $order->id,
            'rater_id'       => $rater->id,
            'rated_user_id'  => $ratedUser->id,
            'score'          => $score,
            'comment'        => $comment,
            'type'           => $type,
        ]);

        // Invalidate trust score cache
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
            $ratings = UserRating::where('rated_user_id', $user->id)->get();

            if ($ratings->isEmpty()) return 0.0;

            $avgRating = $ratings->avg('score');

            // Transaction bonus: 0-5 scale based on completed orders
            $completedCount = \App\Models\Order::where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
            })->where('status', 'completed')->count();

            $transactionBonus = min(5.0, $completedCount / 10);

            // Verification bonus: based on KYC level
            $kycLevel = app(KycService::class)->getVerificationLevel($user);
            $verificationBonus = ($kycLevel / 3) * 5;

            return round(
                ($avgRating * 0.6) + ($transactionBonus * 0.3) + ($verificationBonus * 0.1),
                2
            );
        });
    }
}
