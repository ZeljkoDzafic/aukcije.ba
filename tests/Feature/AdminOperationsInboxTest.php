<?php

declare(strict_types=1);

use App\Models\AdminLog;
use App\Models\Auction;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders admin dashboard inbox and operational queues', function () {
    $this->withoutVite();

    $admin = User::factory()->moderator()->create([
        'name' => 'Admin Operater',
    ]);

    $seller = User::factory()->seller()->create();
    $buyer = User::factory()->buyer()->create();

    UserVerification::query()->create([
        'user_id' => $seller->id,
        'type' => 'id_document',
        'status' => 'pending',
        'document_url' => 'https://example.test/id-front.jpg',
    ]);

    Auction::factory()->create([
        'seller_id' => $seller->id,
        'status' => 'pending_review',
        'title' => 'Aukcija za moderaciju',
    ]);

    $order = Order::factory()->create([
        'seller_id' => $seller->id,
        'buyer_id' => $buyer->id,
    ]);

    $dispute = Dispute::factory()->open()->create([
        'order_id' => $order->id,
        'opened_by_id' => $buyer->id,
        'reason' => 'item_not_received',
        'description' => 'Moderatorska odluka je potrebna.',
    ]);

    AdminLog::query()->create([
        'admin_id' => $admin->id,
        'action' => 'force-kyc-review',
        'target_type' => 'user',
        'target_id' => $seller->id,
        'metadata' => ['note' => 'Dodatna provjera dokumenta'],
    ]);

    AdminLog::query()->create([
        'admin_id' => $admin->id,
        'action' => 'approve',
        'target_type' => 'auction',
        'target_id' => $order->auction_id,
        'metadata' => ['note' => 'Listing odobren nakon pregleda'],
    ]);

    AdminLog::query()->create([
        'admin_id' => $admin->id,
        'action' => 'resolve-dispute',
        'target_type' => 'dispute',
        'target_id' => $dispute->id,
        'metadata' => ['resolution' => 'partial-refund'],
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Admin inbox')
        ->assertSee('KYC queue')
        ->assertSee('Moderacija aukcija')
        ->assertSee('Otvoreni sporovi')
        ->assertSee('Riješi dispute queue');

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('KYC pending')
        ->assertSee('Seller force review');

    $this->actingAs($admin)
        ->get(route('admin.users.show', ['user' => $seller->id]))
        ->assertOk()
        ->assertSee('KYC queue')
        ->assertSee('Decision history')
        ->assertSee('FORCE-KYC-REVIEW')
        ->assertSee('Dodatna provjera dokumenta');

    $this->actingAs($admin)
        ->get(route('admin.auctions.index'))
        ->assertOk()
        ->assertSee('Pending review')
        ->assertSee('Reported')
        ->assertSee('Featured nadzor');

    $this->actingAs($admin)
        ->get(route('admin.disputes.index'))
        ->assertOk()
        ->assertSee('Otvoreni')
        ->assertSee('Riješi sada')
        ->assertSee('Moderatorska odluka je potrebna.');

    $this->actingAs($admin)
        ->get(route('admin.disputes.show', ['dispute' => $dispute->id]))
        ->assertOk()
        ->assertSee('Decision history')
        ->assertSee('RESOLVE-DISPUTE')
        ->assertSee('partial-refund');

    $this->actingAs($admin)
        ->get(route('admin.auctions.show', ['auction' => $order->auction_id]))
        ->assertOk()
        ->assertSee('Decision history')
        ->assertSee('APPROVE')
        ->assertSee('Listing odobren nakon pregleda');
});
