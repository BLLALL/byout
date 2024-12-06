<?php

declare(strict_types=1);

use Database\Seeders\RolesAndPermissionSeeder;

it('seeds roles and permission', function (): void {
    Artisan::call('db:seed', [
        '--class' => RolesAndPermissionSeeder::class,
    ]);

    $this->assertDatabaseHas('roles', ['name' => 'Hotel Owner']);
});