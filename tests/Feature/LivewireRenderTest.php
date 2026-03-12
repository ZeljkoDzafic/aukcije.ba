<?php

declare(strict_types=1);

use App\Livewire\AuctionSearch;
use App\Livewire\Seller\CreateAuctionWizard;
use App\Livewire\WalletManager;
use App\Livewire\Watchlist;
use Livewire\Livewire;

it('renders auction search livewire component', function () {
    Livewire::test(AuctionSearch::class)
        ->assertSee('Pretraga aukcija')
        ->set('query', 'sony')
        ->assertSee('Sony WH-1000XM5');
});

it('renders watchlist livewire component', function () {
    Livewire::test(Watchlist::class)
        ->assertSee('Moje praćene aukcije')
        ->set('filter', 'ended')
        ->assertSee('Vinyl kolekcija EX/YU');
});

it('renders create auction wizard livewire component', function () {
    Livewire::test(CreateAuctionWizard::class)
        ->assertSee('Osnovno')
        ->call('nextStep')
        ->assertSee('URL slike')
        ->set('newImageUrl', 'https://example.test/slika-1.jpg')
        ->call('addImage')
        ->assertSee('slika-1.jpg');
});

it('renders wallet manager livewire component', function () {
    Livewire::test(WalletManager::class)
        ->assertSee('Dopuni wallet')
        ->call('quickDeposit', 50)
        ->assertSet('depositAmount', '50');
});
