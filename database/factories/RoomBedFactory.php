<?php

namespace Database\Factories;

use App\Models\Chalet;
use App\Models\Home;
use App\Models\HotelRooms;
use App\Models\RoomBed;
use Illuminate\Database\Eloquent\Factories\Factory;
use phpDocumentor\Reflection\Types\Collection;

/**
 * @extends Factory<RoomBed>
 */
class RoomBedFactory extends Factory
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
            'bed_type' => fake()->randomElement([
                'single bed',
                'double bed',
                'large double bed',
                'extra large double bed',
                'sofa bed',
                'bunk bed',
            ]),
            'bedable_type' => get_class($accommodation),
            'bedable_id' => $accommodation->id,
        ];
    }
}
