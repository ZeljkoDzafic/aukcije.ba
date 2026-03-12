<?php

declare(strict_types=1);

use Database\Seeders\ContentPageSeeder;
use Database\Seeders\NewsArticleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders public content and news pages', function () {
    $this->seed([
        ContentPageSeeder::class,
        NewsArticleSeeder::class,
    ]);

    $this->withoutVite();

    $routes = [
        route('content.about'),
        route('content.terms'),
        route('content.privacy'),
        route('content.buying'),
        route('content.selling'),
        route('help.index'),
        route('content.show', ['slug' => 'kontakt']),
        route('news.index'),
        route('news.show', ['article' => 'sigurnosni-savjeti-za-kupce']),
    ];

    foreach ($routes as $url) {
        $this->get($url)->assertOk();
    }
});
