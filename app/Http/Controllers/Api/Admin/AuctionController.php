<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Auction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $auctions = Auction::query()
            ->with(['seller', 'category'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $auctions]);
    }

    public function approve(Request $request, Auction $auction): JsonResponse
    {
        $auction->update(['status' => 'active']);

        AdminLog::query()->create([
            'admin_id' => $request->user()->id,
            'action' => 'approve-auction',
            'target_type' => 'auction',
            'target_id' => $auction->id,
        ]);

        return response()->json(['success' => true, 'auction_id' => $auction->id, 'status' => $auction->fresh()->status]);
    }

    public function reject(Request $request, Auction $auction): JsonResponse
    {
        $auction->update(['status' => 'cancelled']);

        AdminLog::query()->create([
            'admin_id' => $request->user()->id,
            'action' => 'reject-auction',
            'target_type' => 'auction',
            'target_id' => $auction->id,
        ]);

        return response()->json(['success' => true, 'auction_id' => $auction->id, 'status' => $auction->fresh()->status]);
    }

    public function feature(Request $request, Auction $auction): JsonResponse
    {
        $auction->update([
            'is_featured' => ! $auction->is_featured,
            'featured_until' => $auction->is_featured ? null : now()->addDays(7),
        ]);

        AdminLog::query()->create([
            'admin_id' => $request->user()->id,
            'action' => 'feature-auction',
            'target_type' => 'auction',
            'target_id' => $auction->id,
            'metadata' => ['is_featured' => $auction->fresh()->is_featured],
        ]);

        return response()->json(['success' => true, 'auction_id' => $auction->id, 'is_featured' => $auction->fresh()->is_featured]);
    }
}
