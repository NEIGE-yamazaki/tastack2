<?php

namespace Database\Factories;

use App\Models\CategoryUserShare;
use App\Models\Category;
use App\Models\ShareGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CategoryUserShare>
 */
class CategoryUserShareFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'share_group_id' => ShareGroup::factory(),
            'permission' => fake()->randomElement(['read', 'edit']),
            'shared_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the share has read permission.
     */
    public function readOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'permission' => 'read',
        ]);
    }

    /**
     * Indicate that the share has edit permission.
     */
    public function editable(): static
    {
        return $this->state(fn (array $attributes) => [
            'permission' => 'edit',
        ]);
    }
}
