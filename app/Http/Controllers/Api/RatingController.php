<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\RatingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function rate(Request $request, Order $order, RatingService $ratingService): JsonResponse
    {
        $this->authorize('view', $order);

        if ($order->status !== 'completed') {
            return response()->json([
                'success' => false,
                'error' => ['message' => 'Ocjena je moguća samo za završene narudžbe.'],
            ], 422);
        }

        $validated = $request->validate([
            'score'   => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $rater = $request->user();

        if (! in_array($rater->id, [$order->buyer_id, $order->seller_id])) {
            return response()->json([
                'success' => false,
                'error' => ['message' => 'Nemaš pravo ocjeniti ovu narudžbu.'],
            ], 403);
        }

        $rating = $ratingService->rateUser(
            $order,
            $rater,
            $validated['score'],
            $validated['comment'] ?? null,
        );

        if ($rating === false) {
            return response()->json([
                'success' => false,
                'error' => ['message' => 'Ocjena za ovu narudžbu je već data.'],
            ], 422);
        }

        $ratingService->calculateTrustScore($rating->rated);

        return response()->json([
            'success' => true,
            'data' => [
                'id'       => $rating->id,
                'score'    => $rating->score,
                'comment'  => $rating->comment,
                'order_id' => $order->id,
            ],
        ], 201);
    }
}
