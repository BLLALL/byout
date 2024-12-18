<?php

namespace Database\Seeders;

use App\Models\AccommodationAmenities;
use App\Models\Chalet;
use App\Models\Home;
use App\Models\Hotel;
use App\Models\HotelRooms;
use App\Models\Owner;
use App\Models\RoomBed;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

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
            'password' => Hash::make('xsarg1234'),
        ]);

        $homeOwner = User::factory()->create([
            'email' => 'HomeOwner@owner.com',
            'name' => 'Home Owner',
            'password' => Hash::make('password'),
        ]);

        $chaletOwner = User::factory()->create([
            'email' => 'ChaletOwner@owner.com',
            'name' => 'Chalet Owner',
            'password' => Hash::make('password'),
        ]);

        $hotelOwnerr = User::factory()->create([
            'email' => 'HotelOwner@owner.com',
            'name' => 'Hotel Owner',
            'password' => Hash::make('password'),
        ]);

        $tourCompanyOwner = User::factory()->create([
            'email' => 'TourCompanyOwner@owner.com',
            'name' => 'Tour Company Owner',
            'password' => Hash::make('password'),
        ]);

        $admin = User::factory()->create([
            'email' => 'admin@owner.com',
            'name' => 'Admoon',
            'password' => Hash::make('password'),
        ]);


        $admin->assignRole('Super Admin');

        $homeOwners = Owner::factory(3)->create([
            'user_id' => function () {
                return User::factory()->create()->assignRole('Home Owner')->id;
            }
        ]);

        $hotelOwners = Owner::factory(3)->create([
            'user_id' => function () {
                return User::factory()->create()->assignRole('Hotel Owner')->id;
            }
        ]);

        $homes = Home::factory(3)->recycle($homeOwners)->create();

        $hotels = new collection();

        foreach ($hotelOwners as $hotelOwner) {
            $hotel = Hotel::factory()->create([
                'owner_id' => $hotelOwner->id
            ]);
            $hotels->add($hotel);
            HotelRooms::factory(3)->for($hotel)->create();
        }
        $chaletOwners = Owner::factory(3)->create([
            'user_id' => function () {
                return User::factory()->create()->assignRole('Chalet Owner')->id;
            }
        ]);
        $chalets = Chalet::factory(3)->recycle($chaletOwners)->create();


        $sarg->assignRole('Regular User');
        $homeOwner->assignRole('Home Owner');
        Owner::factory()->create([
            'user_id' => $homeOwner->id,
        ]);

        $hotelOwnerr->assignRole('Hotel Owner');
        Owner::factory()->create([
            'user_id' => $hotelOwnerr->id,
        ]);

        $chaletOwner->assignRole('Chalet Owner');
        Owner::factory()->create([
            'user_id' => $chaletOwner->id,
        ]);
        $tourCompanyOwner->assignRole('Tour Company Owner');

        Owner::factory()->create([
            'user_id' => $tourCompanyOwner->id,
        ]);

        $hotels = Hotel::all();
        $hotels->each(function ($hotel) {
            $hotel->owner->user->assignRole('Hotel Owner');
        });

        $chalets = Chalet::all();
        $chalets->each(function ($chalet) {
            $chalet->owner->user->assignRole('Chalet Owner');
        });

        $homes = Home::all();
        $homes->each(function ($home) {
            $home->owner->user->assignRole('Home Owner');
        });

        $accommodations = $homes->concat($chalets)->concat(HotelRooms::all());

        $accommodations->each(function ($accommodation) {
            RoomBed::factory(rand(1, 3))
                ->create([
                    'bedable_type' => get_class($accommodation),
                    'bedable_id' => $accommodation->id,
                ]);
            AccommodationAmenities::factory(rand(1, 3))
                ->create([
                    'servable_type' => get_class($accommodation),
                    'servable_id' => $accommodation->id,
                ]);
        });

    }
}
