<?php

namespace Database\Factories;

use App\Models\Chalet;
use App\Models\Favourite;
use App\Models\Home;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Favourite>
 */
class FavouriteFactory extends Factory
{
    protected $model = Favourite::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $favouritableTypes = [
            Home::class,
            Hotel::class,
            Chalet::class,
        ];

        $favouritableType = fake()->randomElement($favouritableTypes);
        $favouritableId = $favouritableType::factory()->create()->id;
        return [
            'user_id' => User::factory(),
            'favorable_id' => $favouritableId,
            'favorable_type' => $favouritableType,
        ];
    }
}
