<?php

namespace Database\Factories;

use App\Enums\AuctionStatus;
use App\Enums\AuctionType;
use App\Models\Auction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Auction>
 */
class AuctionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'seller_id' => User::factory()->seller(),
            'category_id' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'start_price' => fake()->randomFloat(2, 10, 500),
            'current_price' => fake()->randomFloat(2, 10, 500),
            'buy_now_price' => fake()->randomFloat(2, 100, 2000),
            'reserve_price' => null,
            'type' => fake()->randomElement([AuctionType::Standard, AuctionType::BuyNow]),
            'condition' => fake()->randomElement(['new', 'used', 'refurbished']),
            'status' => AuctionStatus::Draft,
            'duration_days' => 7,
            'ends_at' => now()->addDays(7),
            'started_at' => null,
            'ended_at' => null,
            'auto_extension' => true,
            'extension_minutes' => 3,
            'bids_count' => 0,
            'views_count' => 0,
            'watchers_count' => 0,
            'winner_id' => null,
            'last_bid_at' => null,
            'is_featured' => false,
            'featured_until' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AuctionStatus::Active,
            'ends_at' => now()->addDays(3),
            'started_at' => now()->subDays(4),
        ]);
    }

    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AuctionStatus::Finished,
            'ends_at' => now()->subDays(1),
            'ended_at' => now()->subDays(1),
        ]);
    }

    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AuctionStatus::Sold,
            'ends_at' => now()->subDays(1),
            'ended_at' => now()->subDays(1),
            'winner_id' => User::factory(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AuctionStatus::Cancelled,
            'cancelled_at' => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AuctionStatus::Draft,
        ]);
    }

    public function withBids(int $count): static
    {
        return $this->afterCreating(function (Auction $auction) use ($count) {
            $users = User::factory()->buyer()->count($count)->create();
            $price = $auction->start_price;

            foreach ($users as $i => $user) {
                $price += 5;
                $auction->bids()->create([
                    'user_id' => $user->id,
                    'amount' => $price,
                    'is_winning' => $i === $count - 1,
                    'created_at' => now()->subMinutes($count - $i),
                ]);
            }

            $auction->update([
                'current_price' => $price,
                'bids_count' => $count,
                'winner_id' => $users->last()->id,
            ]);
        });
    }

    public function endingSoon(int $minutes = 30): static
    {
        return $this->state(fn (array $attributes) => [
            'ends_at' => now()->addMinutes($minutes),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'featured_until' => now()->addDays(7),
        ]);
    }
}
