<?php

declare(strict_types=1);

use App\Models\Auction;
use App\Models\Order;
use App\Models\User;
use App\Models\WalletTransaction;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Pest PHP test configuration
*/

uses(
    TestCase::class,
)->in('Feature');

uses(
    TestCase::class,
)->in('Unit');

class_alias(WalletTransaction::class, 'WalletTransaction');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| Custom Pest expectations
*/

expect()->extend('toBeValidUuid', function () {
    return $this->toBeString()
        ->and(preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $this->value))->toBeTrue();
});

expect()->extend('toBeValidEmail', function () {
    return $this->toBeString()
        ->and(filter_var($this->value, FILTER_VALIDATE_EMAIL))->toBeTrue();
});

expect()->extend('toBePositiveNumber', function () {
    return $this->toBeNumeric()
        ->and($this->value)->toBeGreaterThan(0);
});

expect()->extend('toBeDecimal', function ($places = 2) {
    $regex = '/^\d+\.\d{'.$places.'}$/';

    return $this->toBeString()
        ->and(preg_match($regex, $this->value))->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| Global test helper functions
*/

function createTestUser($role = 'buyer')
{
    return match ($role) {
        'buyer' => User::factory()->buyer()->create(),
        'seller' => User::factory()->seller()->create(),
        'admin' => User::factory()->admin()->create(),
        default => User::factory()->create(),
    };
}

function createTestAuction($status = 'active', $attributes = [])
{
    return match ($status) {
        'active' => Auction::factory()->active()->create($attributes),
        'draft' => Auction::factory()->draft()->create($attributes),
        'finished' => Auction::factory()->finished()->create($attributes),
        'sold' => Auction::factory()->sold()->create($attributes),
        default => Auction::factory()->create($attributes),
    };
}

function createTestOrder($status = 'pending_payment', $attributes = [])
{
    return match ($status) {
        'pending_payment' => Order::factory()->pendingPayment()->create($attributes),
        'paid' => Order::factory()->paid()->create($attributes),
        'shipped' => Order::factory()->shipped()->create($attributes),
        'delivered' => Order::factory()->delivered()->create($attributes),
        'completed' => Order::factory()->completed()->create($attributes),
        default => Order::factory()->create($attributes),
    };
}

function actingAsUser($user = null, $role = 'buyer')
{
    if (! $user) {
        $user = createTestUser($role);
    }

    return Sanctum::actingAs($user);
}
