<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->where('buyer_id', $request->user()->id)
            ->with(['auction.primaryImage', 'seller', 'shipment'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'per_page'     => $orders->perPage(),
                'total'        => $orders->total(),
            ],
        ]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $order->load([
            'auction.primaryImage',
            'auction.category',
            'seller.profile',
            'shipment',
            'payments',
            'dispute',
            'ratings',
        ]);

        return response()->json([
            'success' => true,
            'data' => array_merge($order->toArray(), [
                'status_badge'          => $order->getStatusBadgeAttribute(),
                'available_actions'     => [
                    'pay'              => $order->needsPayment() && ! $order->isPaymentOverdue(),
                    'confirm_delivery' => $order->isShipped(),
                    'open_dispute'     => in_array($order->status, ['paid', 'shipped', 'delivered']),
                    'leave_rating'     => $order->isCompleted(),
                ],
            ]),
        ]);
    }
}
