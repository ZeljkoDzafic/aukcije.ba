<?php

declare(strict_types=1);

use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\StatisticsOverview;
use App\Livewire\Seller\OrderFulfillment;
use Livewire\Livewire;

it('renders admin category manager livewire component', function () {
    Livewire::test(CategoryManager::class)
        ->assertSee('Hijerarhija kategorija')
        ->call('selectCategory', 'Satovi')
        ->assertSet('selectedCategory', 'Satovi');
});

it('renders admin statistics overview livewire component', function () {
    Livewire::test(StatisticsOverview::class)
        ->assertSee('Users')
        ->set('tab', 'revenue')
        ->assertSee('32.400 BAM, rast 11%');
});

it('renders seller order fulfillment livewire component', function () {
    Livewire::test(OrderFulfillment::class)
        ->call('markShipped')
        ->assertSee('Narudžba je označena kao poslana');
});
