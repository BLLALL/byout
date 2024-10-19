<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\Owner;
use Illuminate\Database\Seeder;
use App\Models\Tour;
use Spatie\Permission\Models\Role;
use App\Models\User;
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

        $vehicles = Vehicle::factory(3)->create([
            'type' => 'van',
            'seats_number' => 7,
        ]);

        


        //        $vehicles = Vehicle::factory(5)->create();
//        $vehicles = Vehicle::factory(5)->create();

        $tourCompanyOwners = Owner::factory(3)->create([
            'user_id' => function () {
                return User::factory()->create()->assignRole('Tour Company Owner')->id;
            }
        ]);
        $tours = Tour::factory(10)->recycle($vehicles)->recycle($tourCompanyOwners)->create();

        $vehicles = Vehicle::factory(3)->create([
            'type' => 'bus',
            'seats_number' => 24
        ]);

        $tours = Tour::factory(10)->recycle($vehicles)->recycle($tourCompanyOwners)->create();


        $vehicles = Vehicle::all();
        $vehicles->each(function ($vehicle) {
            $vehicle->owner->user->assignRole('Tour Company Owner');
        });

        $tours->each(function ($tour) {
            $tour->owner->user->assignRole('Tour Company Owner');
        });

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
