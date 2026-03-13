<?php

declare(strict_types=1);

use App\Livewire\Admin\AuctionDetailManager;
use App\Livewire\Admin\AuctionModeration;
use App\Livewire\Admin\DisputeResolution;
use App\Livewire\Admin\UserDirectory;
use App\Livewire\Admin\UserProfileManager;
use Livewire\Livewire;

it('renders admin user directory livewire component', function () {
    Livewire::test(UserDirectory::class)
        ->set('search', 'amar')
        ->assertSee('Amar Hadžić')
        ->call('toggleSelection', '1')
        ->set('bulkNote', 'Bulk pregled korisnika')
        ->assertSee('Bulk decision note')
        ->call('moderate', 1, 'ban')
        ->assertSee("Akcija 'ban' pripremljena za korisnika Amar Hadžić.");
});

it('renders admin auction moderation livewire component', function () {
    Livewire::test(AuctionModeration::class)
        ->set('filter', 'reported')
        ->assertSee('Rolex Datejust 36')
        ->call('toggleSelection', '1')
        ->set('bulkNote', 'Bulk pregled queue-a')
        ->assertSee('Bulk decision note')
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
        ->assertSee('Toggle seller pristup')
        ->assertSee('Reset lozinke')
        ->set('adminNote', 'Ručna provjera korisničkog profila')
        ->assertSee('Admin decision note');
});

it('renders admin auction detail manager livewire component', function () {
    Livewire::test(AuctionDetailManager::class)
        ->assertSee('Approve')
        ->set('moderationNote', 'Provjera serijskog broja')
        ->assertSee('Napomena moderatora');
});
