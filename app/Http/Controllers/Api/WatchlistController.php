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
        $data = $request->user()
            ->watchlist()
            ->with('category')
            ->latest('auction_watchers.created_at')
            ->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function add(Request $request, Auction $auction): JsonResponse
    {
        $request->user()->watchlist()->syncWithoutDetaching([$auction->id]);

        return response()->json([
            'success' => true,
            'auction_id' => $auction->id,
            'watchers_count' => $auction->watchers()->count(),
        ]);
    }

    public function remove(Request $request, Auction $auction): JsonResponse
    {
        $request->user()->watchlist()->detach($auction->id);

        return response()->json([
            'success' => true,
            'auction_id' => $auction->id,
        ]);
    }
}
