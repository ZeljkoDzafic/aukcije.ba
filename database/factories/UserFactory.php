<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('Password123!'),
            'phone' => fake()->phoneNumber(),
            'phone_verified_at' => null,
            'kyc_level' => 0,
            'trust_score' => 0.00,
            'is_banned' => false,
            'banned_at' => null,
            'ban_reason' => null,
            'remember_token' => Str::random(10),
            'notification_preferences' => null,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }

    public function buyer(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('buyer');
        });
    }

    public function seller(): static
    {
        return $this->state(fn (array $attributes) => [
            'kyc_level' => 2,
        ])->afterCreating(function (User $user) {
            $user->assignRole('seller');
        });
    }

    public function verifiedSeller(): static
    {
        return $this->state(fn (array $attributes) => [
            'kyc_level' => 3,
            'phone_verified_at' => now(),
        ])->afterCreating(function (User $user) {
            $user->assignRole('verified_seller');
        });
    }

    public function admin(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('super_admin');
        });
    }

    public function moderator(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('moderator');
        });
    }

    public function banned(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_banned' => true,
            'banned_at' => now(),
            'ban_reason' => fake()->sentence(),
        ]);
    }

    public function withTrustScore(float $score): static
    {
        return $this->state(fn (array $attributes) => [
            'trust_score' => $score,
        ]);
    }

    public function withWallet(float $balance): static
    {
        return $this->afterCreating(function (User $user) use ($balance) {
            $user->wallet()->create(['balance' => $balance]);
        });
    }
}
