<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\AuctionNotActiveException;
use App\Exceptions\BidTooLowException;
use App\Exceptions\CannotBidOwnAuctionException;
use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use App\Services\BiddingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BidController extends Controller
{
    public function __construct(
        protected BiddingService $biddingService
    ) {}

    /**
     * Place a bid
     */
    public function place(Request $request, Auction $auction): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $user = Auth::user();

        try {
            $bid = $this->biddingService->placeBid(
                $user,
                $auction,
                (float) $request->amount,
                (bool) $request->boolean('is_proxy'),
                $request->filled('max_proxy_amount') ? (float) $request->input('max_proxy_amount') : null
            );

            $auction = $auction->fresh();

            return response()->json([
                'success' => true,
                'id' => $bid->id,
                'amount' => (float) $bid->amount,
                'user_id' => $bid->user_id,
                'data' => [
                    'bid_id' => $bid->id,
                    'current_price' => (float) $auction->current_price,
                    'next_minimum_bid' => (float) $auction->minimum_bid,
                    'auction_ends_at' => optional($auction->ends_at)?->toIso8601String(),
                ],
                'message' => 'Bid placed successfully',
            ]);
        } catch (BidTooLowException $e) {
            return response()->json([
                'success' => false,
                'error' => ['message' => $e->getMessage()],
            ], 400);
        } catch (AuctionNotActiveException $e) {
            return response()->json([
                'success' => false,
                'error' => ['message' => $e->getMessage()],
            ], 410);
        } catch (CannotBidOwnAuctionException $e) {
            return response()->json([
                'success' => false,
                'error' => ['message' => $e->getMessage()],
            ], 403);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Bid placement failed', [
                'auction_id' => $auction->id,
                'user_id' => $user->id,
                'amount' => $request->amount,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => ['message' => 'Licitacija nije uspjela. Molimo pokušajte ponovo.'],
            ], 500);
        }
    }

    /**
     * Get auction bids
     */
    public function index(Auction $auction): JsonResponse
    {
        $bids = $auction->bids()
            ->with('user')
            ->orderBy('amount', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'data' => $bids,
        ]);
    }
}
