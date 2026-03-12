<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // TODO: Add pagination (20/page) — power users can have hundreds of watched auctions.
        // TODO: Include primaryImage, minimum_bid, time_remaining, and is_ending_soon flag.
        // TODO: Add ?filter=active|ended query param support.
        $data = $request->user()
            ->watchlist()
            ->with(['category', 'primaryImage'])
            ->latest('auction_watchers.created_at')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $data->items()]);
    }

    public function add(Request $request, Auction $auction): JsonResponse
    {
        $request->user()->watchlist()->syncWithoutDetaching([$auction->id]);

        // TODO: Increment auction watchers_count atomically:
        //       $auction->increment('watchers_count')  (currently relies on watchers()->count() which hits DB again)
        return response()->json([
            'success' => true,
            'auction_id' => $auction->id,
            'watchers_count' => $auction->watchers()->count(),
        ]);
    }

    public function remove(Request $request, Auction $auction): JsonResponse
    {
        $request->user()->watchlist()->detach($auction->id);

        // TODO: Decrement auction watchers_count atomically:
        //       $auction->decrement('watchers_count')
        return response()->json([
            'success' => true,
            'auction_id' => $auction->id,
        ]);
    }
}
