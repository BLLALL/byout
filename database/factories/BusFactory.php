<?php

namespace Database\Factories;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bus>
 */
class BusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'registration_number' => fake()->numberBetween(111100, 999999),
            'model' => 'suzuki',
            'bus_images' => [
                'https://loremflickr.com/640/480/person',
                'https://loremflickr.com/640/480/',
                ],
            'seats_number' => fake()->numberBetween(2, 160),
            'owner_id' => Owner::factory(),
        ];
    }
}
