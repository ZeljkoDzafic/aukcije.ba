<?php

namespace App\Livewire\Seller;

use App\Models\Order;
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
        if ($this->orderId && Schema::hasTable('orders')) {
            $order = Order::query()->find($this->orderId);

            if ($order) {
                $order->update(['status' => 'shipped']);
                $this->statusMessage = "Narudžba '{$order->id}' je označena kao poslana.";
            }
        }

        $this->markedShipped = true;
    }

    public function render()
    {
        return view('livewire.seller.order-fulfillment');
    }
}
