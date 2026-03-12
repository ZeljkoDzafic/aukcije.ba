<?php

namespace Database\Factories;

use App\Models\Dispute;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dispute>
 */
class DisputeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'opened_by_id' => User::factory(),
            'reason' => fake()->randomElement(['item_not_received', 'not_as_described', 'damaged', 'counterfeit']),
            'description' => fake()->paragraph(),
            'status' => 'open',
            'resolution' => null,
            'resolved_by_id' => null,
            'seller_response' => null,
            'evidence' => [],
            'escalated_at' => null,
            'resolved_at' => null,
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
        ]);
    }

    public function escalated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'escalated',
            'escalated_at' => now(),
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'resolved',
            'resolution' => fake()->randomElement(['full_refund', 'partial_refund', 'release_to_seller']),
            'resolved_at' => now(),
        ]);
    }
}
