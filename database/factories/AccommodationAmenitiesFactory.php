<?php

namespace Database\Factories;

use App\Models\Chalet;
use App\Models\Home;
use App\Models\HotelRooms;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccommodationAmenities>
 */
class AccommodationAmenitiesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $homes = Home::all();
        $hotelRooms = HotelRooms::all();
        $chalets = Chalet::all();

        $accommodations = $homes->concat($hotelRooms)->concat($chalets);
        $accommodation = $accommodations->random();


        return [
            'air_conditioner' => fake()->boolean(90),
            'washing_machine' => fake()->boolean(),
            'dryer' => fake()->boolean(),
            'breakfast' => fake()->boolean(),
            'free_wifi' => fake()->boolean(90),
            'sea_view' => fake()->boolean(),
            'mountain_view' => fake()->boolean(),
            'city_view' => fake()->boolean(),
            'terrace' => fake()->boolean(),
            'balcony' => fake()->boolean(),
            'heating' => fake()->boolean(),
            'coffee_machine' => fake()->boolean(),
            'free_parking' => fake()->boolean(),
            'swimming_pools' => fake()->boolean(),
            'restaurant' => fake()->boolean(),
            'tv' => fake()->boolean(80),
            'reception_service' => fake()->boolean(),
            'cleaning_service' =>fake()->boolean(),
            'garden' => fake()->boolean(),
            'servable_type' => get_class($accommodation),
            'servable_id' => $accommodation->id,
        ];
    }
}
