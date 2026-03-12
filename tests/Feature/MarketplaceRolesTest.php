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
        'role' => 'seller',
    ])->assertRedirect('/seller/dashboard');

    $user = User::query()->where('email', 'dual-role@example.test')->firstOrFail();

    expect($user->hasRole('buyer'))->toBeTrue()
        ->and($user->hasRole('seller'))->toBeTrue();
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
