<?php

namespace Database\Factories;

use App\Models\UserRating;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserRating>
 */
class UserRatingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'rater_id' => User::factory(),
            'rated_id' => User::factory(),
            'score' => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence(),
            'is_visible' => true,
        ];
    }

    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'score' => fake()->numberBetween(4, 5),
        ]);
    }

    public function negative(): static
    {
        return $this->state(fn (array $attributes) => [
            'score' => fake()->numberBetween(1, 2),
        ]);
    }

    public function neutral(): static
    {
        return $this->state(fn (array $attributes) => [
            'score' => 3,
        ]);
    }
}
