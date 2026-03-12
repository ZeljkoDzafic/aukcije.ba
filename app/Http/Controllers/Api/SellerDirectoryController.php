<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * T-1203: Public seller directory — browsable list of active sellers with reputation data.
 */
class SellerDirectoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['seller', 'verified_seller']))
            ->where('is_banned', false)
            ->withCount([
                'auctions as active_auctions_count' => fn ($q) => $q->where('status', 'active'),
            ])
            ->with(['profile:user_id,city,country,avatar'])
            ->select(['id', 'name', 'trust_score', 'seller_reputation_score', 'created_at']);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->whereHas('auctions', fn ($q) => $q
                ->where('status', 'active')
                ->where('category_id', $request->get('category_id'))
            );
        }

        // Sort
        $sort = $request->get('sort', 'reputation');
        match ($sort) {
            'active_auctions' => $query->orderByDesc('active_auctions_count'),
            'newest'          => $query->orderByDesc('created_at'),
            default           => $query->orderByDesc('seller_reputation_score'),
        };

        $sellers = $query->paginate(24);

        return response()->json([
            'data' => $sellers->items(),
            'meta' => [
                'current_page' => $sellers->currentPage(),
                'last_page'    => $sellers->lastPage(),
                'total'        => $sellers->total(),
            ],
        ]);
    }

    public function show(User $user): JsonResponse
    {
        if ($user->is_banned || ! $user->hasAnyRole(['seller', 'verified_seller'])) {
            return response()->json(['error' => ['message' => 'Seller not found.']], 404);
        }

        $activeAuctions = $user->auctions()
            ->where('status', 'active')
            ->with('images')
            ->orderBy('ends_at')
            ->limit(20)
            ->get();

        return response()->json([
            'data' => [
                'id'                       => $user->id,
                'name'                     => $user->name,
                'trust_score'              => $user->trust_score,
                'seller_reputation_score'  => $user->seller_reputation_score,
                'member_since'             => $user->created_at->toDateString(),
                'profile'                  => $user->profile,
                'active_auctions'          => $activeAuctions,
            ],
        ]);
    }
}
