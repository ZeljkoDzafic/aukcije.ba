<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * T-1104: Seller reputation score calculation.
 *
 * Formula:
 *   score = fulfilment_rate × 0.40
 *         + punctuality_rate × 0.30
 *         + (1 - dispute_rate) × 0.20
 *         + response_time_score × 0.10
 *
 * All sub-scores are 0.0 – 1.0; result is 0.00 – 100.00.
 */
class SellerReputationService
{
    /**
     * Recalculate and persist reputation score for one seller.
     */
    public function calculate(User $seller): float
    {
        $stats = $this->gatherStats($seller);

        $score = ($stats['fulfilment_rate'] * 0.40)
            + ($stats['punctuality_rate'] * 0.30)
            + ((1 - $stats['dispute_rate']) * 0.20)
            + ($stats['response_time_score'] * 0.10);

        $score = round(max(0.0, min(1.0, $score)) * 100, 2);

        $seller->updateQuietly([
            'seller_reputation_score'      => $score,
            'seller_reputation_updated_at' => now(),
        ]);

        return $score;
    }

    /**
     * Recalculate for all active sellers (used in scheduled job).
     */
    public function recalculateAll(): int
    {
        $count = 0;

        User::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['seller', 'verified_seller']))
            ->chunk(100, function ($sellers) use (&$count) {
                foreach ($sellers as $seller) {
                    $this->calculate($seller);
                    $count++;
                }
            });

        return $count;
    }

    /**
     * @return array{fulfilment_rate: float, punctuality_rate: float, dispute_rate: float, response_time_score: float}
     */
    private function gatherStats(User $seller): array
    {
        $totalOrders = DB::table('orders')
            ->where('seller_id', $seller->id)
            ->whereIn('status', ['completed', 'cancelled', 'disputed'])
            ->count();

        if ($totalOrders === 0) {
            return [
                'fulfilment_rate'    => 0.5,
                'punctuality_rate'   => 0.5,
                'dispute_rate'       => 0.0,
                'response_time_score' => 0.5,
            ];
        }

        // Fulfilment rate = orders that reached 'completed' or 'shipped'
        $fulfilled = DB::table('orders')
            ->where('seller_id', $seller->id)
            ->whereIn('status', ['completed', 'shipped', 'delivered'])
            ->count();

        $fulfilmentRate = $totalOrders > 0 ? $fulfilled / $totalOrders : 0.0;

        // Punctuality rate = shipped within deadline (5 days)
        $shippedOnTime = DB::table('orders')
            ->join('shipments', 'shipments.order_id', '=', 'orders.id')
            ->where('orders.seller_id', $seller->id)
            ->whereColumn('shipments.created_at', '<=', DB::raw('orders.payment_deadline_at + interval \'5 days\''))
            ->count();

        $shippedTotal = DB::table('orders')
            ->join('shipments', 'shipments.order_id', '=', 'orders.id')
            ->where('orders.seller_id', $seller->id)
            ->count();

        $punctualityRate = $shippedTotal > 0 ? $shippedOnTime / $shippedTotal : 0.5;

        // Dispute rate = disputed / total
        $disputed = DB::table('orders')
            ->where('seller_id', $seller->id)
            ->where('status', 'disputed')
            ->count();

        $disputeRate = min(1.0, $totalOrders > 0 ? $disputed / $totalOrders : 0.0);

        // Response time score — placeholder (0.7 default, deduct for slow responses)
        $responseTimeScore = 0.7;

        return [
            'fulfilment_rate'    => (float) $fulfilmentRate,
            'punctuality_rate'   => (float) $punctualityRate,
            'dispute_rate'       => (float) $disputeRate,
            'response_time_score' => $responseTimeScore,
        ];
    }
}
