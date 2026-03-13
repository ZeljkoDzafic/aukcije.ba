<?php

declare(strict_types=1);

use App\Models\Auction;
use App\Models\SavedSearch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows authenticated users to save a search and view it later', function () {
    $this->withoutVite();

    $user = User::factory()->buyer()->create();

    $this->actingAs($user)
        ->post(route('searches.store'), [
            'query' => 'Rolex',
            'category_slug' => 'satovi',
        ])
        ->assertRedirect(route('searches.index'));

    expect(SavedSearch::query()->where('user_id', $user->id)->count())->toBe(1);

    $this->actingAs($user)
        ->get(route('searches.index'))
        ->assertOk()
        ->assertSee('Rolex')
        ->assertSee('Alert uključen');

    $search = SavedSearch::query()->where('user_id', $user->id)->firstOrFail();

    $this->actingAs($user)
        ->patch(route('searches.toggle', ['search' => $search->id]))
        ->assertSessionHas('status');

    expect($search->fresh()->alert_enabled)->toBeFalse();
});

it('renders homepage discovery blocks from live auction data', function () {
    $this->withoutVite();

    Auction::factory()->active()->create([
        'title' => 'Najviše praćeni sat',
        'watchers_count' => 99,
        'bids_count' => 15,
    ]);

    Auction::factory()->active()->create([
        'title' => 'Uskoro ističe aukcija',
        'ends_at' => now()->addHour(),
        'watchers_count' => 12,
        'bids_count' => 5,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Najviše praćeno')
        ->assertSee('Uskoro ističe')
        ->assertSee('Najviše praćeni sat')
        ->assertSee('Uskoro ističe aukcija');
});
