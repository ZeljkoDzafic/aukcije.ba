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
            ->with(['category', 'primaryImage'])
            ->latest('auction_watchers.created_at')
            ->paginate(20)
            ->through(function (Auction $auction): array {
                return [
                    'id' => $auction->id,
                    'title' => $auction->title,
                    'status' => $auction->status,
                    'current_price' => $auction->current_price,
                    'minimum_bid' => $auction->minimum_bid,
                    'watchers_count' => $auction->watchers_count,
                    'time_remaining' => $auction->time_remaining,
                    'is_ending_soon' => $auction->ends_at?->isFuture() && $auction->ends_at->diffInHours(now()) <= 24,
                    'category' => $auction->category ? [
                        'id' => $auction->category->id,
                        'name' => $auction->category->name,
                    ] : null,
                    'primary_image' => $auction->primaryImage?->url,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ]);
    }

    public function add(Request $request, Auction $auction): JsonResponse
    {
        $attached = $request->user()->watchlist()->syncWithoutDetaching([$auction->id]);

        if (! empty($attached['attached'])) {
            $auction->increment('watchers_count');
        }

        return response()->json([
            'success' => true,
            'auction_id' => $auction->id,
            'watchers_count' => $auction->fresh()->watchers_count,
        ]);
    }

    public function remove(Request $request, Auction $auction): JsonResponse
    {
        $removed = $request->user()->watchlist()->detach($auction->id);

        if ($removed > 0 && $auction->watchers_count > 0) {
            $auction->decrement('watchers_count');
        }

        return response()->json([
            'success' => true,
            'auction_id' => $auction->id,
            'watchers_count' => $auction->fresh()->watchers_count,
        ]);
    }
}
