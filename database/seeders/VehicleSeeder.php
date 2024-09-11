<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\Owner;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
       $owners = Owner::whereRelation('user', 'name', 'Tour Company Owner')->get();

        Vehicle::factory(10)->recycle($owners)->car()->create();

        Vehicle::factory(5)->recycle($owners)->van()->create();

        $busCapacities = [24, 30, 46, 49, 53, 67];

        foreach ($busCapacities as $capacity) {
            Vehicle::factory()->recycle($owners)->create([
                'type' => 'bus',
                'seats_number' => $capacity,
            ]);
        }

        Vehicle::factory(5)->create();

//        Vehicle::factory()->create([
//            'type' => 'bus',
//            'model' => 'Volvo 9700',
//            'seats_number' => 53,
//            'has_wifi' => true,
//            'has_air_conditioner' => true,
//            'has_gps' => true,
//            'has_movie_screens' => true,
//        ]);
//
//        Vehicle::factory()->create([
//            'type' => 'van',
//            'model' => 'Mercedes-Benz Sprinter',
//            'seats_number' => 14,
//            'has_wifi' => true,
//            'has_air_conditioner' => true,
//            'has_gps' => true,
//        ]);
    }
}
