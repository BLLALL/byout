<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HotelRooms>
 */
class HotelRoomsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price' => fake()->numberBetween(10000, 50000),
            'area' => fake()->numberBetween(40, 100),
            'bathrooms_no' => fake()->numberBetween(1, 3),
            'bedrooms_no' => fake()->numberBetween(1, 3),
            'room_images' => [
                "https://loremflickr.com/640/480",
                "https://loremflickr.com/640/480",
                "https://loremflickr.com/640/480",
            ],
            'hotel_id' => Hotel::factory(),
            'available_from' => fake()->dateTimeBetween('now', '+1 day')->format('Y-m-d'),
            'available_until' => fake()->dateTimeBetween('+2 month', '+3 month')->format('Y-m-d'),
            'capacity' => fake()->randomDigitNotNull(),
        ];
    }
}
