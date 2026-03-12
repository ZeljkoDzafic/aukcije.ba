<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\SecondChanceOffer;
use App\Services\AuctionService;
use App\Services\SecondChanceOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * T-1304: Second chance offer — seller offers to next highest bidder after auction ends.
 */
class SecondChanceController extends Controller
{
    public function __construct(
        private readonly SecondChanceOfferService $offerService,
        private readonly AuctionService $auctionService,
    ) {}

    public function offer(Auction $auction): JsonResponse
    {
        try {
            $offer = $this->offerService->offer($auction, Auth::user());
        } catch (\RuntimeException $e) {
            return response()->json(['error' => ['message' => $e->getMessage()]], 422);
        }

        return response()->json(['data' => $offer], 201);
    }

    public function accept(SecondChanceOffer $offer): JsonResponse
    {
        try {
            $this->offerService->accept($offer, Auth::user(), $this->auctionService);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => ['message' => $e->getMessage()]], 422);
        }

        return response()->json(['data' => ['status' => 'accepted']]);
    }

    public function decline(SecondChanceOffer $offer): JsonResponse
    {
        try {
            $this->offerService->decline($offer, Auth::user());
        } catch (\RuntimeException $e) {
            return response()->json(['error' => ['message' => $e->getMessage()]], 422);
        }

        return response()->json(['data' => ['status' => 'declined']]);
    }
}
