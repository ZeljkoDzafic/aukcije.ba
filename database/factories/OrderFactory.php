<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Auction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $buyer = User::factory()->buyer();
        $seller = User::factory()->seller();
        
        return [
            'auction_id' => Auction::factory(),
            'buyer_id' => $buyer,
            'seller_id' => $seller,
            'status' => 'pending_payment',
            'payment_status' => 'pending',
            'total_amount' => fake()->randomFloat(2, 50, 1000),
            'commission_amount' => 0,
            'seller_payout' => 0,
            'payment_gateway' => null,
            'paid_at' => null,
            'payment_deadline_at' => now()->addDays(3),
            'shipping_method' => 'euroexpress',
            'shipping_cost' => 5.00,
            'shipping_address' => fake()->address(),
            'shipping_city' => fake()->city(),
            'shipping_postal_code' => fake()->postcode(),
            'shipping_country' => 'BA',
            'delivered_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
            'cancel_reason' => null,
        ];
    }

    public function pendingPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending_payment',
            'payment_status' => 'pending',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function awaitingShipment(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'awaiting_shipment',
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function shipped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'shipped',
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'payment_status' => 'paid',
            'paid_at' => now(),
            'delivered_at' => now()->subDays(2),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'paid',
            'paid_at' => now(),
            'delivered_at' => now()->subDays(15),
            'completed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancel_reason' => fake()->sentence(),
        ]);
    }

    public function disputed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'disputed',
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}
