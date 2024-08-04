<?php

namespace Database\Factories;

use App\Models\Home;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HomeImage>
 */
class HomeImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $images = Storage::disk('public')->files('images/homes');

        return [
            'home_id' => Home::factory(),
            'image_path' => $this->faker->randomElement($images),
            'type' => $this->faker->randomElement(['exterior', 'interior', 'floor_plan', 'other']),
        ];
    }
}
