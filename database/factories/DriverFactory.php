<?php

namespace Database\Factories;

use App\Models\Owner;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Driver>
 */
class DriverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'license' => 'https://loremflickr.com/640/480/',
            'license_expiry_date'=> fake()->date(),
            'user_id' => User::factory(),
            'owner_id' => Owner::factory(),
        ];
    }
}
