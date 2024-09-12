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

        $hotelowner = User::factory()->create([
            'email' => 'HotelOwner@owner.com',
            'name' => 'Hotel Owner',
            'password' => Hash::make('password'),
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

        $tourCompanyOwner = User::factory()->create([
            'email' => 'TourCompanyOwner@owner.com',
            'name' => 'Tour Company Owner',
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

        $homeOwnerRole = Role::create(['name' => 'Home Owner']);
        $homeOwnerRole->givePermissionTo(['Post Homes', 'Delete Homes']);

        $hotelOwnerRole = Role::create(['name' => 'Hotel Owner']);
        $hotelOwnerRole->givePermissionTo(['Post Rooms', 'Delete Rooms']);
        $hotelOwnerRole->givePermissionTo(['Post Hotels', 'Delete Hotels']);

        $tourCompanyOwnerRole = Role::create(['name' => 'Tour Company Owner']);
        $tourCompanyOwnerRole->givePermissionTo(['Post Buses', 'Delete Buses', 'Post Tours', 'Delete Tours']);

        $chaletOwnerRole = Role::create(['name' => 'Chalet Owner']);
        $chaletOwnerRole->givePermissionTo(['Post Chalets', 'Delete Chalets']);

        $driverRole = Role::create(['name' => 'Driver']);

        $roles = Role::all();

        foreach ($roles as $role) {
            $role->givePermissionTo('Update Own Profile');
        }

        $regularUserRole = Role::create(['name' => 'Regular User']);

        $homeOwners = Owner::factory(3)->asHomeOwner()->create();
        $homes = Home::factory(3)->recycle($homeOwners)->create();

        $tourCompanyOwners = Owner::factory(3)->asTourCompanyOwner()->create();
        $buses = Bus::factory(3)->recycle($tourCompanyOwners)->create();
        $tours = Tour::factory(3)->recycle($tourCompanyOwners)->recycle($buses)->create();

        $hotelOwners = Owner::factory(3)->asHotelOwner()->create();
        $hotels = collect();  // Initialize a collection to hold all hotels

        foreach ($hotelOwners as $hotelOwner) {
            $hotel = Hotel::factory()->create([
                'owner_id' => $hotelOwner->id,
            ]);
            $hotels->push($hotel);  // Add the created hotel to the collection

// Create rooms for the hotel
            HotelRooms::factory(3)->create(['hotel_id' => $hotel->id]);
        }

        $chaletOwners = Owner::factory(3)->asChaletOwner()->create();
        $chalets = Chalet::factory(3)->recycle($chaletOwners)->create();


        $sarg->assignRole($regularUserRole);
        $homeOwner->assignRole($homeOwnerRole);
        Owner::factory()->create([
            'user_id' => $homeOwner->id,
        ]);
        $hotelowner->assignRole($hotelOwnerRole);
        Owner::factory()->create([
            'user_id' => $hotelowner->id,
        ]);
        $chaletOwner->assignRole($chaletOwnerRole);
    Owner::factory()->create([
            'user_id' => $chaletOwner->id,
        ]);
        $tourCompanyOwner->assignRole($tourCompanyOwnerRole);
        Owner::factory()->create([
            'user_id' => $tourCompanyOwner->id,
        ]);

        Document::factory(10)->create();


        Favourite::factory(10)->create();
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
