<?php

declare(strict_types=1);

use App\Models\Auction;
use App\Models\AuctionNotification;
use App\Models\Category;
use App\Models\SavedSearch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates marketplace notifications for matching saved searches', function () {
    $buyer = User::factory()->buyer()->create();
    $category = Category::factory()->create([
        'name' => 'Satovi',
        'slug' => 'satovi',
    ]);

    SavedSearch::query()->create([
        'user_id' => $buyer->id,
        'query' => 'Rolex',
        'category_slug' => 'satovi',
        'alert_enabled' => true,
    ]);

    Auction::factory()->active()->create([
        'category_id' => $category->id,
        'title' => 'Rolex Datejust 41',
    ]);

    $this->artisan('searches:send-alerts')
        ->assertSuccessful();

    expect(AuctionNotification::query()->where('user_id', $buyer->id)->count())->toBe(1);
});
