<?php

namespace Database\Factories;

use App\Models\Chalet;
use App\Models\Home;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reviewableTypes = [
            Home::class,
            Hotel::class,
            Chalet::class,
        ];

        $reviewableType = fake()->randomElement($reviewableTypes);
        $reviewableId = $reviewableType::factory()->create()->id;

        return [
            'rating' => $this->faker->numberBetween(1, 5),
            'user_id' => User::factory(),
            'reviewable_type' => $reviewableType,
            'reviewable_id' => $reviewableId,
        ];
    }
}
