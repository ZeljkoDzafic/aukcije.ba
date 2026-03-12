<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows authenticated users to update notification preferences', function () {
    $this->withoutVite();

    $user = User::factory()->buyer()->create([
        'notification_preferences' => [
            'email_outbid' => true,
            'email_auction_ended' => true,
            'email_ending_soon' => true,
            'email_messages' => true,
            'email_saved_searches' => true,
            'email_shipping_updates' => true,
            'email_disputes' => true,
            'sms_enabled' => false,
            'push_enabled' => true,
        ],
    ]);

    $this->actingAs($user)
        ->get(route('settings.notifications'))
        ->assertOk()
        ->assertSee('Postavke obavijesti');

    $this->actingAs($user)
        ->post(route('settings.notifications.update'), [
            'email_outbid' => '1',
            'email_auction_ended' => '0',
            'email_ending_soon' => '1',
            'email_messages' => '1',
            'email_saved_searches' => '0',
            'email_shipping_updates' => '1',
            'email_disputes' => '0',
            'sms_enabled' => '1',
            'push_enabled' => '1',
        ])
        ->assertRedirect(route('settings.notifications'));

    $preferences = $user->fresh()->notification_preferences;

    expect($preferences['email_outbid'])->toBeTrue()
        ->and($preferences['email_auction_ended'])->toBeFalse()
        ->and($preferences['email_ending_soon'])->toBeTrue()
        ->and($preferences['email_messages'])->toBeTrue()
        ->and($preferences['email_saved_searches'])->toBeFalse()
        ->and($preferences['email_shipping_updates'])->toBeTrue()
        ->and($preferences['email_disputes'])->toBeFalse()
        ->and($preferences['sms_enabled'])->toBeTrue()
        ->and($preferences['push_enabled'])->toBeTrue();
});
