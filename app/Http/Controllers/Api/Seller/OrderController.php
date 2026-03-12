<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use App\Notifications\ItemShippedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->with(['buyer', 'auction', 'shipment'])
            ->where('seller_id', $request->user()->id)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->whereHas('buyer', fn ($b) => $b->where('name', 'like', '%'.$request->string('search').'%'))
                    ->orWhereHas('auction', fn ($a) => $a->where('title', 'like', '%'.$request->string('search').'%'));
            }))
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $orders->items(),
            'meta'    => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'per_page'     => $orders->perPage(),
                'total'        => $orders->total(),
            ],
        ]);
    }

    public function ship(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->seller_id === $request->user()->id, 403);

        if ($order->status === 'shipped') {
            return response()->json([
                'success' => false,
                'error'   => ['message' => 'Narudžba je već označena kao poslana.'],
            ], 422);
        }

        if (! in_array($order->status, ['awaiting_shipment', 'paid'])) {
            return response()->json([
                'success' => false,
                'error'   => ['message' => 'Narudžba mora biti plaćena prije slanja.'],
            ], 422);
        }

        $validated = $request->validate([
            'courier'         => ['required', 'string', 'in:euroexpress,postexpress,bhposta', 'max:100'],
            'tracking_number' => ['required', 'string', 'max:100'],
        ]);

        $shipment = Shipment::query()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'courier'          => $validated['courier'],
                'tracking_number'  => $validated['tracking_number'],
                'status'           => 'in_transit',
                'status_label'     => 'Pošiljka predata kuriru',
                'status_updated_at' => now(),
            ]
        );

        $order->update(['status' => 'shipped']);

        $order->buyer?->notify(new ItemShippedNotification($order->fresh(), $shipment->fresh()));

        return response()->json([
            'success' => true,
            'data'    => [
                'order_id' => $order->id,
                'status'   => $order->fresh()->status,
                'shipment' => $shipment->fresh(),
            ],
        ]);
    }
}
