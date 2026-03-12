<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * T-1503: Admin analytics dashboard — GMV, users, conversion, fraud signals.
 */
class AnalyticsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $period = $request->get('period', 'daily');
        $days   = match ($period) {
            'weekly'  => 7,
            'monthly' => 30,
            default   => 1,
        };

        $cacheKey = "admin_analytics:{$period}";

        $data = Cache::remember($cacheKey, 1800, function () use ($days) {
            $since = now()->subDays($days);

            // GMV
            $gmv = DB::table('orders')
                ->whereIn('status', ['completed', 'shipped', 'delivered'])
                ->where('created_at', '>=', $since)
                ->sum('total_amount');

            // New users
            $newUsers = DB::table('users')
                ->where('created_at', '>=', $since)
                ->count();

            // Active auctions
            $activeAuctions = DB::table('auctions')
                ->where('status', 'active')
                ->count();

            // Auction conversion: sold / (sold + finished)
            $sold = DB::table('auctions')
                ->where('status', 'sold')
                ->where('ended_at', '>=', $since)
                ->count();

            $totalEnded = DB::table('auctions')
                ->whereIn('status', ['sold', 'finished'])
                ->where('ended_at', '>=', $since)
                ->count();

            $conversionRate = $totalEnded > 0 ? round($sold / $totalEnded * 100, 1) : 0;

            // Dispute rate
            $totalOrders = DB::table('orders')->where('created_at', '>=', $since)->count();
            $disputedOrders = DB::table('orders')
                ->where('status', 'disputed')
                ->where('created_at', '>=', $since)
                ->count();
            $disputeRate = $totalOrders > 0 ? round($disputedOrders / $totalOrders * 100, 2) : 0;

            // Top sellers by GMV
            $topSellers = DB::table('orders')
                ->join('users', 'users.id', '=', 'orders.seller_id')
                ->whereIn('orders.status', ['completed', 'shipped', 'delivered'])
                ->where('orders.created_at', '>=', $since)
                ->select('users.id', 'users.name', DB::raw('SUM(total_amount) as gmv'), DB::raw('COUNT(*) as orders'))
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('gmv')
                ->limit(10)
                ->get();

            // Fraud flags pending review
            $pendingFraudFlags = DB::table('admin_logs')
                ->where('action', 'fraud_flag')
                ->whereJsonContains('metadata->review_status', 'pending')
                ->count();

            // Daily GMV trend (last 30 days)
            $gmvTrend = DB::table('orders')
                ->whereIn('status', ['completed', 'shipped', 'delivered'])
                ->where('created_at', '>=', now()->subDays(30))
                ->selectRaw("DATE(created_at) as date, SUM(total_amount) as gmv, COUNT(*) as orders")
                ->groupByRaw('DATE(created_at)')
                ->orderBy('date')
                ->get();

            return [
                'period'              => ['days' => $days, 'since' => $since->toDateString()],
                'gmv'                 => (float) $gmv,
                'new_users'           => $newUsers,
                'active_auctions'     => $activeAuctions,
                'auction_conversion'  => $conversionRate,
                'dispute_rate'        => $disputeRate,
                'top_sellers'         => $topSellers,
                'pending_fraud_flags' => $pendingFraudFlags,
                'gmv_trend'           => $gmvTrend,
            ];
        });

        return response()->json(['data' => $data]);
    }
}
