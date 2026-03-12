<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the main public pages', function () {
    $this->withoutVite();

    $routes = [
        route('home'),
        route('auctions.index'),
        route('categories.index'),
        route('search'),
        route('sellers.index'),
        route('news.index'),
        route('content.about'),
        route('help.index'),
        route('sellers.show', ['user' => User::factory()->seller()->create()->id]),
        route('login'),
        route('register'),
        route('password.request'),
    ];

    foreach ($routes as $url) {
        $this->get($url)->assertOk();
    }
});
