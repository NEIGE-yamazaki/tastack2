<?php

namespace Database\Factories;

use App\Models\ShareGroupMember;
use App\Models\ShareGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShareGroupMember>
 */
class ShareGroupMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'share_group_id' => ShareGroup::factory(),
            'user_id' => User::factory(),
            'joined_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
