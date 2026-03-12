<?php

namespace Database\Factories;

use App\Models\Wallet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'balance' => 0,
            'escrow_balance' => 0,
            'frozen' => false,
            'frozen_at' => null,
            'frozen_reason' => null,
        ];
    }

    public function withBalance(float $balance): static
    {
        return $this->state(fn (array $attributes) => [
            'balance' => $balance,
            'escrow_balance' => 0,
        ]);
    }

    public function frozen(): static
    {
        return $this->state(fn (array $attributes) => [
            'frozen' => true,
            'frozen_at' => now(),
            'frozen_reason' => 'Admin action',
        ]);
    }
}
