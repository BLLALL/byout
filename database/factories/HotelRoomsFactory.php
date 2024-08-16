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
            'title' => fake()->word(),
            'price' => fake()->numberBetween(100, 500),
            'area' => fake()->numberBetween(40, 100),
            'bathrooms_no' => fake()->numberBetween(1, 3),
            'bedrooms_no' => fake()->numberBetween(1, 3),
            'room_images' => [
                "https://loremflickr.com/640/480",
                "https://loremflickr.com/640/480",
                "https://loremflickr.com/640/480",
            ],
            'hotel_id' => Hotel::factory(),
            'is_reserved' => fake()->randomElement([false, true]),
        ];
    }
}
