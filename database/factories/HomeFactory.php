<?php

namespace Database\Factories;

use App\Models\Home;
use App\Models\HomeImage;
use App\Models\Owner;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Home>
 */
class HomeFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 1000, 10000),
            'area' => $this->faker->numberBetween(80, 800),
            'bathrooms_no' => $this->faker->numberBetween(1, 10),
            'bedrooms_no' => $this->faker->numberBetween(1, 10),
            'location' => $this->faker->randomElement(['Cairo', 'Benha', 'Dahab', 'Alexandria', '6 October']),
            'owner_id' => Owner::factory(),
            'avg_rating' => null, // This will be calculated after reviews are created
            'coordinates' => [
                fake()->randomFloat(2, -90, 90),
                fake()->randomFloat(2, -180, 180),
            ],
            'home_images' => [
                "https://loremflickr.com/640/480/",
                "https://loremflickr.com/640/480/",
            ],
            'rent_period' => fake()->randomElement(['weekly', 'monthly', 'yearly']),
            'available_from' => now()->addDays(30),
            'available_until' => now()->addMonths(4),
            'is_available' => true,
        ];
    }




}
