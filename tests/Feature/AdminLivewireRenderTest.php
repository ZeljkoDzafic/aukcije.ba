<?php

declare(strict_types=1);

use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\StatisticsOverview;
use App\Livewire\Seller\OrderFulfillment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

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
    $seller = User::factory()->seller()->create();
    $order = Order::factory()->create([
        'seller_id' => $seller->id,
    ]);

    $this->actingAs($seller);

    Livewire::test(OrderFulfillment::class, ['orderId' => $order->id])
        ->set('courier', 'euroexpress')
        ->set('trackingNumber', 'EE123456789BA')
        ->call('markShipped')
        ->assertSee('Tracking podaci su sačuvani i status pošiljke je ažuriran.');
});
