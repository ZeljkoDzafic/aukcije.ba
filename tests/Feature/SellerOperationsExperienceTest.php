<?php

declare(strict_types=1);

use App\Models\Auction;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders seller order inbox, detail and dispute workflows', function () {
    $this->withoutVite();

    $seller = User::factory()->seller()->create([
        'name' => 'Seller Operater',
    ]);
    $buyer = User::factory()->buyer()->create([
        'name' => 'Kupac Operater',
    ]);

    $auction = Auction::factory()->active()->create([
        'seller_id' => $seller->id,
        'title' => 'Rolex Datejust',
    ]);

    $order = Order::factory()->awaitingShipment()->create([
        'auction_id' => $auction->id,
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'payment_deadline_at' => now()->addHours(6),
    ]);

    Shipment::query()->create([
        'order_id' => $order->id,
        'courier' => 'euroexpress',
        'tracking_number' => 'EE99887766BA',
        'status' => 'in_transit',
    ]);

    Dispute::factory()->open()->create([
        'order_id' => $order->id,
        'opened_by_id' => $buyer->id,
        'reason' => 'item_not_received',
        'description' => 'Kupac traži potvrdu o otpremi.',
    ]);

    $this->actingAs($seller)
        ->get(route('seller.orders.index'))
        ->assertOk()
        ->assertSee('Čeka uplatu')
        ->assertSee('Spremno za slanje')
        ->assertSee('Otvoreni sporovi')
        ->assertSee('EE99887766BA')
        ->assertSee('Spor');

    $this->actingAs($seller)
        ->get(route('seller.orders.show', ['order' => $order->id]))
        ->assertOk()
        ->assertSee('Operativni koraci')
        ->assertSee('Otvori seller spor')
        ->assertSee('item not received');

    $this->actingAs($seller)
        ->get(route('seller.orders.dispute.show', ['order' => $order->id]))
        ->assertOk()
        ->assertSee('Seller akcije')
        ->assertSee('Potvrdi tracking kupcu')
        ->assertSee('Kupac traži potvrdu o otpremi.');
});
