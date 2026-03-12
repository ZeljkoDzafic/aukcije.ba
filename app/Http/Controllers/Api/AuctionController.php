<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuctionController extends Controller
{
    /**
     * List auctions
     */
    public function index(Request $request): JsonResponse
    {
        $query = Auction::with(['seller', 'category', 'primaryImage']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->active();
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by price range
        if ($request->filled('price_min')) {
            $query->where('current_price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('current_price', '<=', $request->price_max);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // Sort
        $sortField = match($request->get('sort')) {
            'ending_soon' => 'ends_at',
            'price_low' => 'current_price',
            'price_high' => 'current_price',
            'newest' => 'created_at',
            'most_bids' => 'bids_count',
            default => 'ends_at'
        };
        
        $sortDirection = $request->get('sort') === 'price_high' ? 'desc' : 'asc';
        
        $query->orderBy($sortField, $sortDirection);

        $auctions = $query->paginate(20);

        return response()->json([
            'data' => $auctions->items(),
            'meta' => [
                'current_page' => $auctions->currentPage(),
                'per_page' => $auctions->perPage(),
                'total' => $auctions->total(),
                'last_page' => $auctions->lastPage(),
            ],
        ]);
    }

    /**
     * Get auction details
     */
    public function show(Auction $auction): JsonResponse
    {
        $auction->load(['seller', 'category', 'images', 'bids.user', 'winningBid']);
        
        return response()->json([
            'data' => $auction,
        ]);
    }
}
