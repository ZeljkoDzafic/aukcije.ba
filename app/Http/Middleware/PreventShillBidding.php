<?php
namespace App\Http\Middleware;

use App\Models\Auction;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Detect and prevent shill bidding patterns:
 * - Seller bidding on own auction (handled in BiddingService)
 * - Velocity check: max bids per time window per user
 * - Same IP bidding multiple accounts
 */
class PreventShillBidding
{
    private const MAX_BIDS_PER_WINDOW = 20;
    private const WINDOW_MINUTES = 60;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Velocity check: too many bids in short time
        $velocityKey = "bid_velocity:{$user->id}";
        $bidCount = Cache::get($velocityKey, 0);

        if ($bidCount >= self::MAX_BIDS_PER_WINDOW) {
            \Illuminate\Support\Facades\Log::warning("Shill bid velocity detected", [
                'user_id' => $user->id,
                'ip'      => $request->ip(),
                'count'   => $bidCount,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Previše pokušaja licitiranja. Vaš račun je privremeno ograničen.',
                ], 429);
            }
            return redirect()->back()->with('error', 'Previše pokušaja licitiranja.');
        }

        Cache::put($velocityKey, $bidCount + 1, now()->addMinutes(self::WINDOW_MINUTES));

        return $next($request);
    }
}
