<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Pest PHP test configuration
*/

uses(
    Tests\TestCase::class,
)->in('Feature');

uses(
    Tests\TestCase::class,
)->in('Unit');

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
    $regex = '/^\d+\.\d{' . $places . '}$/';
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

function createTestUser($role = 'buyer') {
    return match($role) {
        'buyer' => \App\Models\User::factory()->buyer()->create(),
        'seller' => \App\Models\User::factory()->seller()->create(),
        'admin' => \App\Models\User::factory()->admin()->create(),
        default => \App\Models\User::factory()->create(),
    };
}

function createTestAuction($status = 'active', $attributes = []) {
    return match($status) {
        'active' => \App\Models\Auction::factory()->active()->create($attributes),
        'draft' => \App\Models\Auction::factory()->draft()->create($attributes),
        'finished' => \App\Models\Auction::factory()->finished()->create($attributes),
        'sold' => \App\Models\Auction::factory()->sold()->create($attributes),
        default => \App\Models\Auction::factory()->create($attributes),
    };
}

function createTestOrder($status = 'pending_payment', $attributes = []) {
    return match($status) {
        'pending_payment' => \App\Models\Order::factory()->pendingPayment()->create($attributes),
        'paid' => \App\Models\Order::factory()->paid()->create($attributes),
        'shipped' => \App\Models\Order::factory()->shipped()->create($attributes),
        'delivered' => \App\Models\Order::factory()->delivered()->create($attributes),
        'completed' => \App\Models\Order::factory()->completed()->create($attributes),
        default => \App\Models\Order::factory()->create($attributes),
    };
}

function actingAsUser($user = null, $role = 'buyer') {
    if (!$user) {
        $user = createTestUser($role);
    }
    
    return \Laravel\Sanctum\Sanctum::actingAs($user);
}
