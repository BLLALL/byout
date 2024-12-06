<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

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


        $homeOwnerRole = Role::create(['name' => 'Home Owner']);
        $homeOwnerRole->givePermissionTo(['Post Homes', 'Delete Homes']);

        $hotelOwnerRole = Role::create(['name' => 'Hotel Owner']);
        $hotelOwnerRole->givePermissionTo(['Post Rooms', 'Delete Rooms']);
        $hotelOwnerRole->givePermissionTo(['Post Hotels', 'Delete Hotels']);


        $chaletOwnerRole = Role::create(['name' => 'Chalet Owner']);


        $chaletOwnerRole->givePermissionTo(['Post Chalets', 'Delete Chalets']);

        Role::create(['name' => 'Driver']);

        $regularUserRole = Role::create(['name' => 'Regular User']);
    }
}
