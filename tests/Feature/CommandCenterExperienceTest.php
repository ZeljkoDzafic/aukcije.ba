<?php

declare(strict_types=1);

use App\Models\Auction;
use App\Models\AuctionNotification;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\Message;
use App\Models\SavedSearch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders filtered notifications and buyer command center widgets', function () {
    $this->withoutVite();

    $buyer = User::factory()->buyer()->create();

    AuctionNotification::query()->create([
        'user_id' => $buyer->id,
        'type' => 'saved_search_match',
        'title' => 'Saved search pogodak',
        'body' => 'Pronađen je novi artikal za tvoju pretragu.',
    ]);

    AuctionNotification::query()->create([
        'user_id' => $buyer->id,
        'type' => 'message',
        'title' => 'Nova poruka',
        'body' => 'Kupac/seller komunikacija je ažurirana.',
    ]);

    AuctionNotification::query()->create([
        'user_id' => $buyer->id,
        'type' => 'dispute_updated',
        'title' => 'Spor je ažuriran',
        'body' => 'Potrebna je nova provjera informacija za narudžbu.',
        'data' => ['order_id' => 'demo-order'],
    ]);

    SavedSearch::query()->create([
        'user_id' => $buyer->id,
        'query' => 'Rolex',
        'alert_enabled' => true,
    ]);

    $order = Order::factory()->pendingPayment()->create([
        'buyer_id' => $buyer->id,
        'payment_deadline_at' => now()->addHours(12),
    ]);

    Dispute::factory()->open()->create([
        'order_id' => $order->id,
        'opened_by_id' => $buyer->id,
    ]);

    Message::query()->create([
        'sender_id' => null,
        'receiver_id' => $buyer->id,
        'auction_id' => null,
        'message_type' => 'system',
        'content' => 'Sistemska poruka za dashboard.',
        'is_read' => false,
    ]);

    $this->actingAs($buyer)
        ->get(route('notifications.index', ['filter' => 'searches']))
        ->assertOk()
        ->assertSee('Saved search pogodak')
        ->assertSee('1')
        ->assertDontSee('Nova poruka');

    $this->actingAs($buyer)
        ->get(route('notifications.index', ['filter' => 'searches', 'sort' => 'latest']))
        ->assertOk()
        ->assertSee('Najnovije')
        ->assertSee('Po hitnosti');

    $this->actingAs($buyer)
        ->get(route('notifications.index', ['filter' => 'disputes']))
        ->assertOk()
        ->assertSee('Spor je ažuriran')
        ->assertSee('Traži reakciju')
        ->assertSee('Otvori spor');

    $this->actingAs($buyer)
        ->post(route('notifications.read-filtered'), ['filter' => 'searches'])
        ->assertSessionHas('status');

    expect(
        AuctionNotification::query()->where('user_id', $buyer->id)->where('type', 'saved_search_match')->first()?->fresh()?->read_at
    )->not->toBeNull();

    $this->actingAs($buyer)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Komandni centar')
        ->assertSee('Prioriteti')
        ->assertSee('Nadlicitirani ste')
        ->assertSee('Rok plaćanja uskoro')
        ->assertSee('Otvoreni sporovi')
        ->assertSee('Nepročitane poruke')
        ->assertSee('Nove obavijesti')
        ->assertSee('Spremljene pretrage');
});

it('renders seller command center widgets', function () {
    $this->withoutVite();

    $seller = User::factory()->seller()->create();

    AuctionNotification::query()->create([
        'user_id' => $seller->id,
        'type' => 'shipment',
        'title' => 'Pošiljka ažurirana',
        'body' => 'Narudžba čeka novu akciju.',
    ]);

    Message::query()->create([
        'sender_id' => null,
        'receiver_id' => $seller->id,
        'auction_id' => null,
        'message_type' => 'system',
        'content' => 'Seller poruka.',
        'is_read' => false,
    ]);

    SavedSearch::query()->create([
        'user_id' => $seller->id,
        'query' => 'Omega',
        'alert_enabled' => true,
    ]);

    $sellerOrder = Order::factory()->awaitingShipment()->create([
        'seller_id' => $seller->id,
        'buyer_id' => User::factory()->buyer()->create()->id,
    ]);

    Dispute::factory()->open()->create([
        'order_id' => $sellerOrder->id,
        'opened_by_id' => $sellerOrder->buyer_id,
    ]);

    $this->actingAs($seller)
        ->get(route('seller.dashboard'))
        ->assertOk()
        ->assertSee('Prodajni komandni centar')
        ->assertSee('Operativni prioriteti')
        ->assertSee('Narudžbe za slanje')
        ->assertSee('Storefront kapacitet')
        ->assertSee('Featured kvota')
        ->assertSee('Draft spremnost')
        ->assertSee('Stari draftovi')
        ->assertSee('Aukcije bez slika')
        ->assertSee('Nepročitane poruke')
        ->assertSee('Nove obavijesti')
        ->assertSee('Spremljene pretrage');
});

it('renders seller listing readiness signals with actionable ctas', function () {
    $this->withoutVite();

    $seller = User::factory()->seller()->create();

    Auction::factory()->draft()->create([
        'seller_id' => $seller->id,
        'title' => 'Stari draft sat',
        'description' => 'Kratko',
        'shipping_info' => null,
        'created_at' => now()->subDays(10),
    ]);

    Auction::factory()->active()->create([
        'seller_id' => $seller->id,
        'title' => 'Aktivna bez slika',
        'description' => 'Premalo',
        'shipping_info' => null,
    ]);

    $this->actingAs($seller)
        ->get(route('seller.auctions.index'))
        ->assertOk()
        ->assertSee('Seller readiness')
        ->assertSee('Dovrši draft')
        ->assertSee('Dodaj slike')
        ->assertSee('Dopuni opis')
        ->assertSee('Dodaj dostavu');
});
