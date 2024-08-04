<?php

namespace Database\Seeders;

use App\Models\Home;
use App\Models\HomeImage;
use App\Models\Review;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory(5)->create();

        $homes = Home::factory(10)->recycle($user)->create();

        Review::factory(10)->recycle($user)->recycle($homes)->create();

        HomeImage::factory(10)->recycle($homes)->create();
    }
}
