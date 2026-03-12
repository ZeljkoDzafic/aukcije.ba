<?php

declare(strict_types=1);

namespace App\Livewire\Seller;

use App\Models\Order;
use App\Models\Message;
use App\Models\Shipment;
use App\Notifications\ItemShippedNotification;
use App\Services\AdminAuditService;
use App\Services\MarketplaceNotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class OrderFulfillment extends Component
{
    public ?string $orderId = null;

    public string $trackingNumber = '';

    public string $courier = '';

    public bool $markedShipped = false;

    public string $statusMessage = '';

    public function markShipped(): void
    {
        $this->validate([
            'courier'         => ['required', 'string', 'in:euroexpress,postexpress,bhposta', 'max:100'],
            'trackingNumber'  => ['required', 'string', 'max:100'],
        ]);

        if (! $this->orderId || ! Schema::hasTable('orders')) {
            $this->markedShipped = true;

            return;
        }

        $order = Order::query()->with('shipment')->find($this->orderId);

        if (! $order || $order->seller_id !== Auth::id()) {
            $this->addError('orderId', 'Narudžba nije pronađena.');

            return;
        }

        if ($order->status === 'shipped') {
            $this->statusMessage = 'Narudžba je već označena kao poslana.';
            $this->markedShipped = true;

            return;
        }

        $shipmentData = [
            'courier'        => $this->courier,
            'tracking_number' => $this->trackingNumber,
            'status'         => 'in_transit',
        ];

        foreach ([
            'status_label'      => 'Pošiljka predata kuriru',
            'status_updated_at' => now(),
            'tracking_events'   => [[
                'status'      => 'in_transit',
                'description' => 'Pošiljka predata kuriru',
                'location'    => $order->shipping_city,
                'timestamp'   => now()->toIso8601String(),
            ]],
        ] as $column => $value) {
            if (Schema::hasColumn('shipments', $column)) {
                $shipmentData[$column] = $value;
            }
        }

        $shipment = Shipment::query()->updateOrCreate(
            ['order_id' => $order->id],
            $shipmentData
        );

        $order->update(['status' => 'shipped']);

        $order->buyer?->notify(new ItemShippedNotification($order->fresh(), $shipment->fresh()));

        app(AdminAuditService::class)->record(
            Auth::id(),
            'seller-marked-shipped',
            'order',
            $order->id,
            [
                'courier' => $this->courier,
                'tracking_number' => $this->trackingNumber,
            ]
        );

        if ($order->buyer) {
            app(MarketplaceNotificationService::class)->notify(
                $order->buyer,
                'shipment',
                'Pošiljka je poslana',
                'Prodavač je označio tvoju narudžbu kao poslanu i unio tracking broj.',
                [
                    'order_id' => $order->id,
                    'auction_id' => $order->auction_id,
                    'tracking_number' => $this->trackingNumber,
                ]
            );

            if (Schema::hasTable('messages')) {
                Message::query()->create([
                    'sender_id' => null,
                    'receiver_id' => $order->buyer->id,
                    'auction_id' => $order->auction_id,
                    'message_type' => 'system',
                    'content' => "Sistemska poruka: narudžba #{$order->id} je označena kao poslana. Tracking broj: {$this->trackingNumber}.",
                    'metadata' => [
                        'order_id' => $order->id,
                        'tracking_number' => $this->trackingNumber,
                        'courier' => $this->courier,
                    ],
                    'is_read' => false,
                ]);
            }
        }

        $this->statusMessage = "Narudžba '{$order->id}' je označena kao poslana.";
        $this->markedShipped = true;
    }

    public function render(): View
    {
        return view('livewire.seller.order-fulfillment');
    }
}
