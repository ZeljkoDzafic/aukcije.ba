<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers seller accounts with buyer and seller access', function () {
    $this->withoutVite();

    $this->post(route('register'), [
        'name' => 'Dual Role Seller',
        'email' => 'dual-role@example.test',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'marketplace_focus' => 'seller',
    ])->assertRedirect('/seller/dashboard');

    $user = User::query()->where('email', 'dual-role@example.test')->firstOrFail();

    expect($user->hasRole('buyer'))->toBeTrue()
        ->and($user->hasRole('seller'))->toBeTrue()
        ->and($user->profile?->primary_marketplace_focus)->toBe('seller');
});

it('registers buyer-focused accounts without losing future seller option', function () {
    $this->withoutVite();

    $this->post(route('register'), [
        'name' => 'Buyer Focus',
        'email' => 'buyer-focus@example.test',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'marketplace_focus' => 'buyer',
    ])->assertRedirect('/dashboard');

    $user = User::query()->where('email', 'buyer-focus@example.test')->firstOrFail();

    expect($user->hasRole('buyer'))->toBeTrue()
        ->and($user->hasRole('seller'))->toBeFalse()
        ->and($user->profile?->primary_marketplace_focus)->toBe('buyer');
});

it('preserves buyer access when admin grants seller access', function () {
    $user = User::factory()->buyer()->create();
    $admin = User::factory()->moderator()->create();

    $this->actingAs($admin)
        ->putJson("/api/v1/admin/users/{$user->id}/role", ['role' => 'seller'])
        ->assertOk()
        ->assertJsonPath('data.roles.0', 'buyer');

    $user->refresh();

    expect($user->hasRole('buyer'))->toBeTrue()
        ->and($user->hasRole('seller'))->toBeTrue();
});
