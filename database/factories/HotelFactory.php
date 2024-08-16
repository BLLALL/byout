<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hotel>
 */
class HotelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'location' => fake()->randomElement(['Cairo', 'Benha', 'Dahab', 'Alexandria', '6 October']),
            'coordinates' => [
                fake()->randomFloat(2, -90, 90),
                fake()->randomFloat(2, -180, 180),
            ],
            'hotel_images' => [
                "https://loremflickr.com/640/480/person",
                "https://loremflickr.com/640/480/person",
            ],
            'hotel_rooms' => fake()->numberBetween(10, 20),
            'user_id' => User::factory(),
        ];
    }
}
