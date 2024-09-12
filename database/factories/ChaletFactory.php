<?php

namespace Database\Factories;

use App\Models\Chalet;
use App\Models\Home;
use App\Models\Owner;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chalet>
 */
class ChaletFactory extends Factory
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
            'price' => $this->faker->numberBetween(1000, 10000),
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
            'chalet_images' => [
                "https://loremflickr.com/640/480/",
                "https://loremflickr.com/640/480/",
            ],
            'rent_period' => fake()->randomElement(['week', '3 days', '2 weeks']),
            'air_conditioning' => true,
            'sea_view' => true,
            'max_occupancy' => fake()->randomElement(['3 nights', '1 week', '2 weeks']),
            'available_from' => fake()->dateTimeBetween('now', '+2 month')->format('Y-m-d'),
            'available_until' => fake()->dateTimeBetween('+2 month', '+3 month')->format('Y-m-d'),
            'is_available' => true,

        ];
    }

    public function configure() {
        return $this->afterCreating(function (Chalet $chalet){
            //create a number of reviews for this home
            $reviews = Review::factory()
                ->create([
                    'reviewable_id' => $chalet->id,
                    'reviewable_type' => Chalet::class,
                    'user_id' => User::factory()
                ]);


//            calculate and set avg_rating
            $chalet->updateReviewStatistics();
        });
    }
}
