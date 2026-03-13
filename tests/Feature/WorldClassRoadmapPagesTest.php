<?php

declare(strict_types=1);

use App\Models\AdminLog;
use App\Models\Auction;
use App\Models\AuctionNotification;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the seller directory with reputation data', function () {
    $this->withoutVite();

    $seller = User::factory()->verifiedSeller()->create([
        'name' => 'Top Seller',
        'trust_score' => 4.8,
    ]);

    UserProfile::query()->create([
        'user_id' => $seller->id,
        'full_name' => 'Top Seller',
        'city' => 'Sarajevo',
        'bio' => 'Specijaliziran za satove i kolekcionarske artikle.',
    ]);

    Auction::factory()->active()->create([
        'seller_id' => $seller->id,
        'title' => 'Omega Speedmaster',
    ]);

    $this->get(route('sellers.index'))
        ->assertOk()
        ->assertSee('Top Seller')
        ->assertSee('Sarajevo')
        ->assertSee('Specijaliziran za satove');
});

it('renders and marks marketplace notifications as read', function () {
    $this->withoutVite();

    $buyer = User::factory()->buyer()->create();

    $notification = AuctionNotification::query()->create([
        'user_id' => $buyer->id,
        'type' => 'message',
        'title' => 'Nova poruka',
        'body' => 'Prodavač vam je poslao novu poruku.',
        'data' => ['auction_id' => 'demo'],
    ]);

    $this->actingAs($buyer)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Nova poruka')
        ->assertSee('Prodavač vam je poslao novu poruku.');

    $this->actingAs($buyer)
        ->post(route('notifications.read', ['notification' => $notification->id]))
        ->assertSessionHas('status');

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('renders admin activity audit trail', function () {
    $this->withoutVite();

    $admin = User::factory()->moderator()->create([
        'name' => 'Admin Operater',
    ]);

    AdminLog::query()->create([
        'admin_id' => $admin->id,
        'action' => 'approve',
        'target_type' => 'auction',
        'target_id' => $admin->id,
        'metadata' => ['note' => 'Sve izgleda uredno'],
    ]);

    $this->actingAs($admin)
        ->get(route('admin.activity.index'))
        ->assertOk()
        ->assertSee('Audit trail')
        ->assertSee('approve')
        ->assertSee('Sve izgleda uredno');
});

it('renders admin kyc backoffice workflow', function () {
    $this->withoutVite();

    $admin = User::factory()->moderator()->create();
    $seller = User::factory()->seller()->create([
        'name' => 'Milan K.',
        'email' => 'milan@example.test',
    ]);

    UserVerification::query()->create([
        'user_id' => $seller->id,
        'type' => 'id_document',
        'status' => 'pending',
        'document_url' => 'https://example.test/front.jpg',
        'notes' => 'Čeka pregled',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.kyc.index'))
        ->assertOk()
        ->assertSee('KYC Verifikacije')
        ->assertSee('Milan K.')
        ->assertSee('front.jpg');
});
