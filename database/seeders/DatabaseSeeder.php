<?php

namespace Database\Seeders;

use App\Models\Home;
use App\Models\HomeFavourite;
use App\Models\Hotel;
use App\Models\HotelRooms;
use App\Models\Review;
use App\Models\Tour;
use App\Models\TourReservation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $sarg = User::factory()->create([
            'email' => 'xsarg22@gmail.com',
            'name' => 'ahmedsarg',
            'password' => Hash::make('xsarg1234')
        ]);

        $belal = User::factory()->create([
            'email' => 'bamer8353@gmail.com',
            'name' => 'belal',
            'password' => Hash::make('password')
        ]);

        Permission::create(['name' => 'Post Homes']);
        Permission::create(['name' => 'Delete Homes']);
        Permission::create(['name' => 'Post Rooms']);
        Permission::create(['name' => 'Delete Rooms']);
        Permission::create(['name' => 'Post Tours']);
        Permission::create(['name' => 'Delete Tours']);

        $homeOwnerRole = Role::create(['name' => 'Home Owner']);
        $homeOwnerRole->givePermissionTo(['Post Homes', 'Delete Homes']);

        $hotelOwnerRole = Role::create(['name' => 'Hotel Owner']);
        $hotelOwnerRole->givePermissionTo(['Post Rooms', 'Delete Rooms']);

        $tourCompanyOwnerRole = Role::create(['name' => 'Tour Company Owner']);
        $tourCompanyOwnerRole->givePermissionTo(['Post Tours', 'Delete Tours']);

        $regularUserRole = Role::create(['name' => 'Regular User']);

        $homeOwners = User::factory(5)->asHomeOwner()->create();
        $homes = Home::factory(10)->recycle($homeOwners)->create();

        $tourCompanyOwners = User::factory(5)->asTourCompanyOwner()->create();
        $tours = Tour::factory(10)->recycle($tourCompanyOwners)->create();

        $hotelOwners = User::factory(5)->asHotelOwner()->create();
        foreach ($hotelOwners as $hotelOwner) {
            $hotel = Hotel::factory()->recycle($hotelOwners)->create([
                'user_id' => $hotelOwner->id
            ]);
            $hotelRooms = HotelRooms::factory(3)->recycle($hotel)->create();
        }


        TourReservation::factory(25)->recycle($tours)->recycle($tourCompanyOwners)->create();

        $regularUsers = User::factory(5)->asRegularUser()->create();
        HomeFavourite::factory(20)->recycle($regularUsers)->recycle($homes)->create();

        $sarg->assignRole($regularUserRole);
        $belal->assignRole($regularUserRole);

        Review::factory(10)->recycle($regularUsers)->recycle($homes)->create();
    }
}
