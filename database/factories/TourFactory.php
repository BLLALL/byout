<?php

namespace Database\Factories;

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
            'departure_time' => $this->faker->time('h:i A'),
            'arrival_time' => $this->faker->time('h:i A'),
            'tour_company_id' => TourCompany::factory(),
            'seat_position' =>
                $this->faker->randomElement(['a1', 'a2', 'a3', 'a4',
                    'b1', 'b2', 'b3', 'b4',
                    'c1', 'c2', 'c3', 'c4',
                    'd1', 'd2', 'd3', 'd4',
                    'e1', 'e2', 'e3', 'e4',
                    'f1', 'f2', 'f3', 'f4',
                    'g1', 'g2', 'g3', 'g4',
                    'h1', 'h2', 'h3', 'h4',]),
            'traveller_gender' => $this->faker->randomElement(['male', 'female']),
        ];
    }
}
