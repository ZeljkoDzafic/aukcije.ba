<?php

declare(strict_types=1);

use App\Models\Message;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('renders public health endpoint', function () {
    $this->get('/health')
        ->assertOk()
        ->assertJsonStructure([
            'status',
            'checks' => ['app', 'database', 'cache'],
            'timestamp',
        ]);
});

it('renders api health endpoint', function () {
    $this->getJson('/api/v1/health')
        ->assertOk()
        ->assertJsonStructure([
            'status',
            'timestamp',
        ]);
});

it('returns authenticated profile payload', function () {
    $user = User::factory()->create();
    Wallet::factory()->create([
        'user_id' => $user->id,
        'balance' => 125.50,
    ]);
    UserProfile::query()->create([
        'user_id' => $user->id,
        'full_name' => 'Test User',
        'city' => 'Sarajevo',
        'country' => 'BA',
    ]);

    Sanctum::actingAs($user);

    $this->getJson('/api/v1/user/profile')
        ->assertOk()
        ->assertJsonPath('data.user.id', $user->id)
        ->assertJsonPath('data.profile.full_name', 'Test User')
        ->assertJsonPath('data.wallet.balance', '125.50');
});

it('updates authenticated profile payload', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'phone' => null,
    ]);

    Sanctum::actingAs($user);

    $this->putJson('/api/v1/user/profile', [
        'name' => 'New Name',
        'phone' => '+38761111222',
        'full_name' => 'New Name',
        'bio' => 'Trusted marketplace user',
        'city' => 'Mostar',
        'country' => 'BA',
    ])
        ->assertOk()
        ->assertJsonPath('data.user.name', 'New Name')
        ->assertJsonPath('data.profile.city', 'Mostar');

    expect($user->fresh()->name)->toBe('New Name')
        ->and($user->fresh()->phone)->toBe('+38761111222');

    $profile = UserProfile::query()->where('user_id', $user->id)->first();

    expect($profile)->not->toBeNull()
        ->and($profile->full_name)->toBe('New Name')
        ->and($profile->bio)->toBe('Trusted marketplace user')
        ->and($profile->city)->toBe('Mostar');
});

it('stores and lists authenticated messages', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    Sanctum::actingAs($sender);

    $this->postJson('/api/v1/user/messages', [
        'receiver_id' => $receiver->id,
        'content' => 'Pozdrav, da li je artikal dostupan?',
    ])
        ->assertCreated()
        ->assertJsonPath('data.sender_id', $sender->id)
        ->assertJsonPath('data.receiver_id', $receiver->id)
        ->assertJsonPath('data.content', 'Pozdrav, da li je artikal dostupan?');

    $message = Message::query()->first();

    expect($message)->not->toBeNull()
        ->and($message->sender_id)->toBe($sender->id)
        ->and($message->receiver_id)->toBe($receiver->id);

    $this->getJson('/api/v1/user/messages')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.content', 'Pozdrav, da li je artikal dostupan?');
});
