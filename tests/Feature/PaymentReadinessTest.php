<?php

declare(strict_types=1);

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('exposes payment callback routes used by hosted gateways', function () {
    $this->get(route('payment.success'))
        ->assertRedirect(route('wallet.index').'?deposit=success');

    $this->get(route('payment.cancel'))
        ->assertRedirect(route('wallet.index').'?deposit=cancelled');
});

it('marks order as paid only once when duplicate payment webhooks arrive', function () {
    $buyer = User::factory()->buyer()->create();
    $seller = User::factory()->seller()->create();
    $order = Order::factory()->pendingPayment()->create([
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'total_amount' => 150,
    ]);

    $payment = Payment::query()->create([
        'order_id' => $order->id,
        'gateway' => 'stripe',
        'transaction_id' => 'stripe_tx_test_1',
        'amount' => 150,
        'currency' => 'BAM',
        'status' => 'pending',
        'metadata' => ['order_id' => $order->id],
    ]);

    $payload = [
        'transaction_id' => 'stripe_tx_test_1',
        'order_id' => $order->id,
        'status' => 'success',
    ];

    $this->postJson('/api/v1/webhooks/payment/stripe', $payload, [
        'X-Payment-Signature' => 'valid-signature',
    ])->assertOk();

    $order->refresh();
    expect($order->status)->toBe('awaiting_shipment')
        ->and($order->payment_status)->toBe('paid');

    $firstPaidAt = $order->paid_at?->toISOString();

    $this->postJson('/api/v1/webhooks/payment/stripe', $payload, [
        'X-Payment-Signature' => 'valid-signature',
    ])->assertOk();

    $order->refresh();
    expect($order->paid_at?->toISOString())->toBe($firstPaidAt);
    expect($payment->fresh()->status)->toBe('success');
});

it('rejects invalid payment webhook signatures', function () {
    $this->postJson('/api/v1/webhooks/payment/stripe', [
        'transaction_id' => 'stripe_tx_invalid',
        'status' => 'success',
    ])->assertUnauthorized();
});

it('shows buyer order actions based on current order state', function () {
    $buyer = User::factory()->buyer()->create();
    $seller = User::factory()->seller()->create();
    $order = Order::factory()->shipped()->create([
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
    ]);

    Sanctum::actingAs($buyer);

    $this->getJson("/api/v1/user/orders/{$order->id}")
        ->assertOk()
        ->assertJsonPath('data.available_actions.confirm_delivery', true)
        ->assertJsonPath('data.available_actions.open_dispute', true)
        ->assertJsonPath('data.available_actions.pay', false);
});
