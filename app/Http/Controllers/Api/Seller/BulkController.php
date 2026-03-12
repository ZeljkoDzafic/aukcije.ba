<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Services\BulkAuctionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * T-1302: Bulk auction operations for sellers.
 */
class BulkController extends Controller
{
    public function __construct(private readonly BulkAuctionService $bulkService) {}

    public function publishDrafts(Request $request): JsonResponse
    {
        $data = $request->validate([
            'auction_ids'   => ['required', 'array', 'min:1', 'max:50'],
            'auction_ids.*' => ['required', 'uuid'],
        ]);

        $result = $this->bulkService->publishDrafts(Auth::user(), $data['auction_ids']);

        return response()->json(['data' => $result]);
    }

    public function endActive(Request $request): JsonResponse
    {
        $data = $request->validate([
            'auction_ids'   => ['required', 'array', 'min:1', 'max:50'],
            'auction_ids.*' => ['required', 'uuid'],
        ]);

        $result = $this->bulkService->endActive(Auth::user(), $data['auction_ids']);

        return response()->json(['data' => $result]);
    }

    public function clone(Request $request): JsonResponse
    {
        $data = $request->validate([
            'auction_ids'   => ['required', 'array', 'min:1', 'max:20'],
            'auction_ids.*' => ['required', 'uuid'],
        ]);

        $result = $this->bulkService->cloneAuctions(Auth::user(), $data['auction_ids']);

        return response()->json(['data' => $result], 201);
    }
}
