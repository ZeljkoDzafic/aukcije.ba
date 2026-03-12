<?php

declare(strict_types=1);

use App\Models\ContentPage;
use App\Models\NewsArticle;
use App\Models\User;
use Database\Seeders\ContentPageSeeder;
use Database\Seeders\NewsArticleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds public content pages and news articles', function () {
    $this->seed([
        ContentPageSeeder::class,
        NewsArticleSeeder::class,
    ]);

    expect(ContentPage::query()->where('slug', 'o-nama')->exists())->toBeTrue()
        ->and(ContentPage::query()->where('slug', 'politika-privatnosti')->exists())->toBeTrue()
        ->and(NewsArticle::query()->where('slug', 'sigurnosni-savjeti-za-kupce')->exists())->toBeTrue();
});

it('allows moderator to open and save cms content', function () {
    $moderator = User::factory()->moderator()->create();

    $this->actingAs($moderator)
        ->get(route('admin.content.pages.index'))
        ->assertOk();

    $this->actingAs($moderator)
        ->post(route('admin.content.pages.store'), [
            'title' => 'Kontakt',
            'slug' => 'kontakt',
            'page_type' => 'company',
            'excerpt' => 'Kontakt podaci platforme.',
            'body' => '<p>Techentis s.p.</p>',
            'is_published' => 1,
        ])
        ->assertRedirect(route('admin.content.pages.index'));

    $this->actingAs($moderator)
        ->post(route('admin.content.news.store'), [
            'title' => 'Nova obavijest',
            'slug' => 'nova-obavijest',
            'excerpt' => 'Kratka obavijest.',
            'body' => '<p>Sadržaj vijesti.</p>',
            'is_published' => 1,
        ])
        ->assertRedirect(route('admin.content.news.index'));

    expect(ContentPage::query()->where('slug', 'kontakt')->exists())->toBeTrue()
        ->and(NewsArticle::query()->where('slug', 'nova-obavijest')->exists())->toBeTrue();
});
