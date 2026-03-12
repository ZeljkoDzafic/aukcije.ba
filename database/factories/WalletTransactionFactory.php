<?php

namespace Database\Factories;

use App\Models\WalletTransaction;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WalletTransaction>
 */
class WalletTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'wallet_id' => Wallet::factory(),
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['deposit', 'withdrawal', 'payment', 'refund', 'escrow_hold', 'escrow_release']),
            'amount' => fake()->randomFloat(2, -500, 500),
            'balance_after' => fake()->randomFloat(2, 0, 2000),
            'description' => fake()->sentence(),
            'reference_type' => null,
            'reference_id' => null,
        ];
    }

    public function deposit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'deposit',
            'amount' => fake()->randomFloat(2, 10, 500),
        ]);
    }

    public function withdrawal(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'withdrawal',
            'amount' => fake()->randomFloat(2, -500, -10),
        ]);
    }

    public function payment(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'payment',
            'amount' => fake()->randomFloat(2, -1000, -10),
        ]);
    }

    public function refund(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'refund',
            'amount' => fake()->randomFloat(2, 10, 500),
        ]);
    }

    public function escrowHold(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'escrow_hold',
            'amount' => fake()->randomFloat(2, -1000, -50),
        ]);
    }

    public function escrowRelease(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'escrow_release',
            'amount' => fake()->randomFloat(2, 50, 1000),
        ]);
    }
}
