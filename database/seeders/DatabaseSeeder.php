<?php

namespace Database\Seeders;

use App\Models\Home;
use App\Models\HomeImage;
use App\Models\Review;
use App\Models\Tour;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;
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

        Permission::create(['name' => 'Post Homes']);
        Permission::create(['name' => 'Delete Homes']);
        Permission::create(['name' => 'Post Rooms']);
        Permission::create(['name' => 'Delete Rooms']);
        Permission::create(['name' => 'Post Tours']);
        Permission::create(['name' => 'Delete Tours']);

        $role = Role::create(['name' => 'Home Owner']);
        $role->givePermissionTo(['Post Homes', 'Delete Homes']);

        $role = Role::create(['name' => 'Hotel Owner']);
        $role->givePermissionTo(['Post Rooms', 'Delete Rooms']);


        $role = Role::create(['name' => 'Tour Company Owner']);
        $role->givePermissionTo(['Post Tours', 'Delete Tours']);

        $user = User::factory(5)->asHomeOwner()->create();

        $homes = Home::factory(10)->recycle($user)->create();

        $user = User::factory(5)->asTourCompanyOwner()->create();

        Tour::factory(10)->recycle($user)->create();

        $role = Role::create(['name' => 'Regular User']);

        $user = User::factory(5)->asRegularUser()->create();

        $sarg->assignRole('Regular User');

        Review::factory(10)->recycle($user)->recycle($homes)->create();



    }
}
