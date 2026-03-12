<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * T-1300: Seller analytics — GMV, sell-through rate, top items, dispute rate.
 */
class StatsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $seller = Auth::user();
        $days   = (int) $request->get('days', 30);
        $days   = in_array($days, [7, 30, 90], true) ? $days : 30;

        $cacheKey = "seller_stats:{$seller->id}:{$days}";

        $stats = Cache::remember($cacheKey, 3600, function () use ($seller, $days) {
            $since = now()->subDays($days);

            // GMV: total value of completed orders
            $gmv = DB::table('orders')
                ->where('seller_id', $seller->id)
                ->whereIn('status', ['completed', 'shipped', 'delivered'])
                ->where('created_at', '>=', $since)
                ->sum('total_amount');

            // Sell-through: sold auctions / total ended auctions
            $endedAuctions = DB::table('auctions')
                ->where('seller_id', $seller->id)
                ->whereIn('status', ['sold', 'finished'])
                ->where('ended_at', '>=', $since)
                ->count();

            $soldAuctions = DB::table('auctions')
                ->where('seller_id', $seller->id)
                ->where('status', 'sold')
                ->where('ended_at', '>=', $since)
                ->count();

            $sellThroughRate = $endedAuctions > 0 ? round($soldAuctions / $endedAuctions * 100, 1) : 0;

            // Average days to sell
            $avgDaysToSell = DB::table('auctions')
                ->where('seller_id', $seller->id)
                ->where('status', 'sold')
                ->where('ended_at', '>=', $since)
                ->whereNotNull('ended_at')
                ->selectRaw('AVG(EXTRACT(EPOCH FROM (ended_at - created_at)) / 86400) as avg_days')
                ->value('avg_days');

            // Dispute rate
            $totalOrders = DB::table('orders')
                ->where('seller_id', $seller->id)
                ->where('created_at', '>=', $since)
                ->count();

            $disputedOrders = DB::table('orders')
                ->where('seller_id', $seller->id)
                ->where('status', 'disputed')
                ->where('created_at', '>=', $since)
                ->count();

            $disputeRate = $totalOrders > 0 ? round($disputedOrders / $totalOrders * 100, 2) : 0;

            // Top performing auctions by final price
            $topAuctions = DB::table('auctions')
                ->where('seller_id', $seller->id)
                ->where('status', 'sold')
                ->where('ended_at', '>=', $since)
                ->orderByDesc('current_price')
                ->limit(5)
                ->select(['id', 'title', 'current_price', 'bids_count', 'ended_at'])
                ->get();

            // Active auctions count
            $activeCount = DB::table('auctions')
                ->where('seller_id', $seller->id)
                ->where('status', 'active')
                ->count();

            return [
                'period_days'       => $days,
                'gmv'               => (float) $gmv,
                'sell_through_rate' => $sellThroughRate,
                'avg_days_to_sell'  => $avgDaysToSell ? round((float) $avgDaysToSell, 1) : null,
                'dispute_rate'      => $disputeRate,
                'total_orders'      => $totalOrders,
                'active_auctions'   => $activeCount,
                'top_auctions'      => $topAuctions,
                'reputation_score'  => $seller->seller_reputation_score,
            ];
        });

        return response()->json(['data' => $stats]);
    }
}
