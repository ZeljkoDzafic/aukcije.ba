<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Services\AuctionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AuctionController extends Controller
{
    public function __construct(protected AuctionService $auctionService) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'string'],
            'start_price' => ['required', 'numeric', 'min:0.01'],
            'reserve_price' => ['nullable', 'numeric', 'min:0'],
            'buy_now_price' => ['nullable', 'numeric', 'min:0'],
            'type' => ['nullable', 'string'],
            'condition' => ['nullable', 'string'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:30'],
            'auto_extension' => ['nullable', 'boolean'],
        ]);

        try {
            $auction = $this->auctionService->createAuction($request->user(), $validated);

            if ($request->boolean('publish', false)) {
                $auction = $this->auctionService->publishAuction($auction);
            }

            return response()->json([
                'id' => $auction->id,
                'title' => $auction->title,
                'status' => $auction->status instanceof \BackedEnum ? $auction->status->value : (string) $auction->status,
            ], 201);
        } catch (\RuntimeException $exception) {
            return response()->json([
                'error' => 'Auction limit reached',
                'message' => $exception->getMessage(),
            ], 403);
        }
    }

    public function update(Request $request, Auction $auction): JsonResponse
    {
        abort_unless($auction->seller_id === $request->user()->id, 403);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'string'],
            'start_price' => ['sometimes', 'numeric', 'min:0.01'],
            'reserve_price' => ['nullable', 'numeric', 'min:0'],
            'buy_now_price' => ['nullable', 'numeric', 'min:0'],
            'type' => ['nullable', 'string'],
            'condition' => ['nullable', 'string'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:30'],
            'status' => ['nullable', 'string'],
        ]);

        if (array_key_exists('start_price', $validated)) {
            $validated['current_price'] = $validated['start_price'];
        }

        if (array_key_exists('duration_days', $validated)) {
            $validated['ends_at'] = now()->addDays((int) $validated['duration_days']);
        }

        $auction->update(collect($validated)->filter(fn ($value, $column) => Schema::hasColumn('auctions', $column))->all());

        return response()->json([
            'success' => true,
            'data' => $auction->fresh(),
        ]);
    }

    public function destroy(Request $request, Auction $auction): JsonResponse
    {
        abort_unless($auction->seller_id === $request->user()->id, 403);

        try {
            $this->auctionService->cancelAuction($auction);
        } catch (\RuntimeException $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], 403);
        }

        return response()->json([
            'success' => true,
            'auction_id' => $auction->id,
            'status' => $auction->fresh()->status instanceof \BackedEnum ? $auction->fresh()->status->value : (string) $auction->fresh()->status,
        ]);
    }
}
