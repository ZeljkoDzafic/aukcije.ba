<?php

use App\Livewire\Admin\AuctionModeration;
use App\Livewire\Admin\AuctionDetailManager;
use App\Livewire\Admin\DisputeResolution;
use App\Livewire\Admin\UserProfileManager;
use App\Livewire\Admin\UserDirectory;
use Livewire\Livewire;

it('renders admin user directory livewire component', function () {
    Livewire::test(UserDirectory::class)
        ->set('search', 'amar')
        ->assertSee('Amar Hadžić')
        ->call('moderate', 1, 'ban')
        ->assertSee("Akcija 'ban' pripremljena za korisnika Amar Hadžić.");
});

it('renders admin auction moderation livewire component', function () {
    Livewire::test(AuctionModeration::class)
        ->set('filter', 'reported')
        ->assertSee('Rolex Datejust 36')
        ->call('applyAction', 1, 'approve')
        ->assertSee("Akcija 'approve' pripremljena za aukciju Rolex Datejust 36.");
});

it('renders admin dispute resolution livewire component', function () {
    Livewire::test(DisputeResolution::class)
        ->call('resolve', 'partial-refund')
        ->assertSee('Pripremljena akcija: partial-refund.');
});

it('renders admin user profile manager livewire component', function () {
    Livewire::test(UserProfileManager::class)
        ->assertSee('Promijeni rolu')
        ->assertSee('Reset lozinke');
});

it('renders admin auction detail manager livewire component', function () {
    Livewire::test(AuctionDetailManager::class)
        ->assertSee('Approve')
        ->set('moderationNote', 'Provjera serijskog broja')
        ->assertSee('Napomena moderatora');
});
