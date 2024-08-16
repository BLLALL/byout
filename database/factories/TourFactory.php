<?php

namespace Database\Factories;

use App\Models\Home;
use App\Models\Tour;
use App\Models\TourCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price' => $this->faker->numberBetween(100, 800),
            'source' => $this->faker->word(),
            'destination' => $this->faker->word(),
            'departure_time' => $this->faker->dateTimeBetween(now(), '+30 day'),
            'arrival_time' => $this->faker->dateTimeBetween('+30 day', '+1 month'),
            'tour_company_id' => TourCompany::factory(),
        ];
    }

}
