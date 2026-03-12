<?php

declare(strict_types=1);

use App\Models\Auction;
use App\Models\Bid;
use App\Models\Dispute;
use App\Models\Message;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

it('renders search results from database auctions', function () {
    $this->withoutVite();

    Auction::factory()->active()->create([
        'title' => 'Canon AE-1 Program',
        'description' => 'Analogni fotoaparat u odličnom stanju',
        'location' => 'Sarajevo',
    ]);

    $this->get(route('search', ['q' => 'Canon']))
        ->assertOk()
        ->assertSee('Canon AE-1 Program');
});

it('renders watchlist, messages and buyer orders from database', function () {
    $this->withoutVite();

    $buyer = User::factory()->buyer()->create([
        'name' => 'Kupac Test',
    ]);
    $seller = User::factory()->seller()->create([
        'name' => 'Prodavac Test',
    ]);

    $auction = Auction::factory()->active()->create([
        'seller_id' => $seller->id,
        'title' => 'Omega Seamaster',
        'current_price' => 2750.00,
    ]);

    $buyer->watchlist()->attach($auction->id);

    Message::query()->create([
        'sender_id' => $seller->id,
        'receiver_id' => $buyer->id,
        'auction_id' => $auction->id,
        'content' => 'Pozdrav, artikal je spreman za pregled.',
        'is_read' => false,
    ]);

    Order::factory()->create([
        'auction_id' => $auction->id,
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'status' => 'awaiting_shipment',
        'payment_status' => 'paid',
        'total_amount' => 2750.00,
        'amount' => 2750.00,
        'shipping_city' => 'Mostar',
    ]);

    $paymentDeadlineOrder = Order::factory()->pendingPayment()->create([
        'auction_id' => $auction->id,
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'payment_deadline_at' => now()->addHours(8),
        'shipping_city' => 'Sarajevo',
    ]);

    Dispute::factory()->open()->create([
        'order_id' => $paymentDeadlineOrder->id,
        'opened_by_id' => $buyer->id,
        'reason' => 'item_not_received',
        'description' => 'Paket još nije isporučen.',
    ]);

    Shipment::query()->create([
        'order_id' => $paymentDeadlineOrder->id,
        'courier' => 'euroexpress',
        'tracking_number' => 'EE123456789BA',
        'status' => 'in_transit',
    ]);

    Bid::query()->create([
        'auction_id' => $auction->id,
        'user_id' => $buyer->id,
        'amount' => 2750.00,
        'is_winning' => true,
        'is_proxy' => false,
        'is_auto' => false,
    ]);

    $this->actingAs($buyer);

    $this->get(route('watchlist.index'))
        ->assertOk()
        ->assertSee('Omega Seamaster');

    $this->get(route('messages.index'))
        ->assertOk()
        ->assertSee('Prodavac Test')
        ->assertSee('Pozdrav, artikal je spreman za pregled.');

    $this->post(route('messages.store'), [
        'receiver_id' => $seller->id,
        'auction_id' => $auction->id,
        'content' => 'Šaljem dodatni link sa referencom.',
        'attachment_name' => 'Detaljna galerija',
        'attachment_url' => 'https://example.test/galerija',
    ])->assertRedirect();

    $this->post(route('messages.store'), [
        'receiver_id' => $seller->id,
        'auction_id' => $auction->id,
        'content' => 'Dodajem i PDF prilog.',
        'attachment_file' => UploadedFile::fake()->create('specifikacija.pdf', 64, 'application/pdf'),
    ])->assertRedirect();

    $this->get(route('messages.index'))
        ->assertOk()
        ->assertSee('Detaljna galerija')
        ->assertSee('Šaljem dodatni link sa referencom.')
        ->assertSee('specifikacija.pdf');

    $this->get(route('orders.index'))
        ->assertOk()
        ->assertSee('Omega Seamaster')
        ->assertSee('Mostar')
        ->assertSee('Timeline')
        ->assertSee('Otvoren spor')
        ->assertSee('item_not_received')
        ->assertSee('Otvori detalj spora');

    $dispute = $paymentDeadlineOrder->fresh()->dispute;

    expect($dispute)->not->toBeNull();

    $this->get(route('orders.dispute.show', ['order' => $paymentDeadlineOrder->id]))
        ->assertOk()
        ->assertSee('Detalj spora')
        ->assertSee('Paket još nije isporučen.')
        ->assertSee('EE123456789BA')
        ->assertSee('Komunikacija i evidencija')
        ->assertSee('Sljedeći koraci')
        ->assertSee('Otvori poruke sa prodavačem');

    $this->get(route('bids.index'))
        ->assertOk()
        ->assertSee('Moje licitacije')
        ->assertSee('Omega Seamaster')
        ->assertSee('Vodite');
});
