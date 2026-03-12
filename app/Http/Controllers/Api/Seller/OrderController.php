<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->with(['buyer', 'auction', 'shipment'])
            ->where('seller_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function ship(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->seller_id === $request->user()->id, 403);

        $validated = $request->validate([
            'courier' => ['required', 'string', 'max:100'],
            'tracking_number' => ['required', 'string', 'max:100'],
        ]);

        $shipment = Shipment::query()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'courier' => $validated['courier'],
                'tracking_number' => $validated['tracking_number'],
                'status' => 'in_transit',
                'status_label' => 'Pošiljka predata kuriru',
                'status_updated_at' => now(),
            ]
        );

        $order->update(['status' => 'shipped']);

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $order->id,
                'status' => $order->fresh()->status,
                'shipment' => $shipment,
            ],
        ]);
    }
}
