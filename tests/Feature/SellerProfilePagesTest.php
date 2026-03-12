<?php

declare(strict_types=1);

use App\Models\Auction;
use App\Models\Order;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserRating;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders public seller profile with auctions and ratings', function () {
    $seller = User::factory()->seller()->create([
        'name' => 'Seller Profil',
        'trust_score' => 4.9,
    ]);

    UserProfile::query()->create([
        'user_id' => $seller->id,
        'full_name' => 'Seller Profil',
        'city' => 'Sarajevo',
        'country' => 'BiH',
    ]);

    Auction::factory()->active()->create([
        'seller_id' => $seller->id,
        'title' => 'Rolex Explorer',
    ]);

    Auction::factory()->active()->create([
        'seller_id' => $seller->id,
        'title' => 'Omega Seamaster',
        'is_featured' => true,
        'watchers_count' => 32,
    ]);

    Auction::factory()->active()->endingSoon(20)->create([
        'seller_id' => $seller->id,
        'title' => 'Tag Heuer Monaco',
    ]);

    Auction::factory()->active()->create([
        'seller_id' => $seller->id,
        'title' => 'Seiko Alpinist',
    ]);

    $buyer = User::factory()->buyer()->create();
    $order = Order::factory()->create([
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
    ]);

    UserRating::query()->create([
        'order_id' => $order->id,
        'rater_id' => $buyer->id,
        'rated_id' => $seller->id,
        'score' => 5,
        'comment' => 'Odlična saradnja.',
        'is_visible' => true,
    ]);

    $this->get(route('sellers.show', ['user' => $seller->id]))
        ->assertOk()
        ->assertSee('Seller Profil')
        ->assertSee('Rolex Explorer')
        ->assertSee('Istaknuto iz seller ponude')
        ->assertSee('Kolekcije po kategorijama')
        ->assertSee('Uskoro završava kod ovog prodavača')
        ->assertSee('Novo dodano kod ovog prodavača')
        ->assertSee('Tag Heuer Monaco')
        ->assertSee('Seiko Alpinist')
        ->assertSee('Odlična saradnja.');
});
