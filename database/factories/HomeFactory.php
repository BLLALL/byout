<?php

namespace Database\Factories;

use App\Models\Home;
use App\Models\HomeImage;
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
            'price' => $this->faker->numberBetween(40000, 10000000),
            'area' => $this->faker->numberBetween(80, 800),
            'bathrooms_no' => $this->faker->numberBetween(1, 10),
            'bedrooms_no' => $this->faker->numberBetween(1, 10),
            'location' => $this->faker->randomElement(['Cairo', 'Benha', 'Dahab', 'Alexandria', '6 October']),
            'user_id' => User::factory(),
            'avg_rating' => null, // This will be calculated after reviews are created
            'coordinates' => [
                fake()->randomFloat(2, -90, 90),
                fake()->randomFloat(2, -180, 180),
            ],
            'home_images' => [
                "https://loremflickr.com/cache/resized/65535_52894684044_4fa4df88d6_b_640_480_nofilter.jpg",
                "https://loremflickr.com/cache/resized/65535_52966824287_f28aed51c7_z_640_480_nofilter.jpg",
                ],
            'rent_period' => fake()->randomElement(['weekly', 'monthly', 'yearly']),
        ];
    }

    public function configure() {
        return $this->afterCreating(function (Home $home){
            //create a number of reviews for this home
           $reviews = Review::factory()
                ->count($this->faker->numberBetween(0, 10))
                ->create([
                    'home_id' => $home->id,
                    'user_id' => User::factory()
                ]);

//            calculate and set avg_rating
            $home->calcAvgRatingAndCount();
        });
    }
}
