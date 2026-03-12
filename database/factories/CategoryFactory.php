<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'parent_id' => null,
            'name' => ucfirst($name),
            'slug' => str($name)->slug()->value(),
            'icon' => fake()->randomElement(['tag', 'camera', 'watch', 'car']),
            'sort_order' => fake()->numberBetween(0, 50),
            'is_active' => true,
        ];
    }
}
