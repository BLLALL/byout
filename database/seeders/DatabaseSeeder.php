<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Chalet;
use App\Models\Document;
use App\Models\Home;
use App\Models\Favourite;
use App\Models\Hotel;
use App\Models\HotelRooms;
use App\Models\Owner;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Collection;

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
        Permission::create(['name' => 'Post Homes']);
        Permission::create(['name' => 'Delete Homes']);
        Permission::create(['name' => 'Post Hotels']);
        Permission::create(['name' => 'Delete Hotels']);
        Permission::create(['name' => 'Post Rooms']);
        Permission::create(['name' => 'Delete Rooms']);
        Permission::create(['name' => 'Post Buses']);
        Permission::create(['name' => 'Delete Buses']);
        Permission::create(['name' => 'Post Tours']);
        Permission::create(['name' => 'Delete Tours']);
        Permission::create(['name' => 'Post Chalets']);
        Permission::create(['name' => 'Delete Chalets']);
        Permission::create(['name' => 'Update Own Profile']);
        Permission::create(['name' => 'Post Vehicles']);
        Permission::create(['name' => 'Delete Vehicles']);


        $tourCompanyOwnerRole = Role::create(['name' => 'Tour Company Owner']);
        $tourCompanyOwnerRole->givePermissionTo(['Post Vehicles', 'Delete Vehicles', 'Post Tours', 'Delete Tours']);

        $adminRole = Role::create(['name' => 'Super Admin']);

        $adminRole->givePermissionTo(Permission::all());

        $admin->assignRole($adminRole);

        $homeOwnerRole = Role::create(['name' => 'Home Owner']);
        $homeOwnerRole->givePermissionTo(['Post Homes', 'Delete Homes']);

        $hotelOwnerRole = Role::create(['name' => 'Hotel Owner']);
        $hotelOwnerRole->givePermissionTo(['Post Rooms', 'Delete Rooms']);
        $hotelOwnerRole->givePermissionTo(['Post Hotels', 'Delete Hotels']);


        $chaletOwnerRole = Role::create(['name' => 'Chalet Owner']);


        $chaletOwnerRole->givePermissionTo(['Post Chalets', 'Delete Chalets']);

        Role::create(['name' => 'Driver']);

        $regularUserRole = Role::create(['name' => 'Regular User']);

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


        $sarg->assignRole($regularUserRole);
        $homeOwner->assignRole($homeOwnerRole);
        Owner::factory()->create([
            'user_id' => $homeOwner->id,
        ]);

        $hotelOwnerr->assignRole($hotelOwnerRole);
        Owner::factory()->create([
            'user_id' => $hotelOwnerr->id,
        ]);

        $chaletOwner->assignRole($chaletOwnerRole);
        Owner::factory()->create([
            'user_id' => $chaletOwner->id,
        ]);
        $tourCompanyOwner->assignRole($tourCompanyOwnerRole);

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


        // Document::factory(10)->create();


        // Favourite::factory(10)->create();
        //// Create reviews for homes
        //$homes->each(function ($home) use ($regularUsers) {
        //Review::factory()->count(3)->for($home, 'reviewable')->recycle($regularUsers)->create();
        //});
        //
        //// Create reviews for hotels
        //$hotels->each(function ($hotel) use ($regularUsers) {
        //Review::factory()->count(3)->for($hotel, 'reviewable')->recycle($regularUsers)->create();
        //});
        //
        //// Create reviews for chalets
        //$chalets->each(function ($chalet) use ($regularUsers) {
        //Review::factory()->count(3)->for($chalet, 'reviewable')->recycle($regularUsers)->create();
        //});
    }
}
