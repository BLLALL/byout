<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TourReservation>
 */
class TourReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'tour_id' => Tour::factory(),
            'seat_positions' => fake()->randomElement([
                'b1', 'b2', 'b3', 'b4',
                'c1', 'c2', 'c3', 'c4',
                'd1', 'd2', 'd3', 'd4',
                'e1', 'e2', 'e3', 'e4',
                'f1', 'f2', 'f3', 'f4',
                'g1', 'g2', 'g3', 'g4',
                'h1', 'h2', 'h3', 'h4',
            ]),
            'gender' => fake()->randomElement(['male', 'female']),
        ];
    }
}


