<?php

use App\Livewire\Admin\FeatureFlags;
use App\Models\FeatureFlag;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Livewire\Livewire;

it('renders admin feature flags livewire component', function () {
    Livewire::test(FeatureFlags::class)
        ->set('name', 'proxy_bidding')
        ->set('description', 'Proxy bidding toggle')
        ->set('isActive', true)
        ->call('save')
        ->assertSee('proxy_bidding')
        ->assertSee('Upravljanje zastavicama');
});

it('registers the feature blade directive', function () {
    if (! Schema::hasTable('feature_flags')) {
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(false);
            $table->string('description')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    FeatureFlag::query()->firstOrCreate(
        ['name' => 'proxy_bidding'],
        ['description' => 'Proxy bidding toggle', 'is_active' => true]
    );

    $rendered = Blade::render('@feature("proxy_bidding")<span>visible</span>@endfeature');

    expect($rendered)->toContain('visible');
});
