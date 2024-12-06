<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionSeeder::class);
    $this->seed(\Database\Seeders\DatabaseSeeder::class);
});

it('gets the correct status code for unauthenticated user', function (): void {
    getJson('api/homes')->assertStatus(status: Response::HTTP_UNAUTHORIZED);
});


it('gets the correct status code for authenticated users', function (): void {

    $user = User::factory()->create(['preferred_currency' => 'SYP']);
    Sanctum::actingAs($user, ['*']);
    getJson('api/homes')->assertStatus(Response::HTTP_OK);
});

it('filters the homes according to its price', function (): void {

    $user = User::factory()->create(['preferred_currency' => 'SYP']);
    Sanctum::actingAs($user);

    $response = $this->getJson('api/homes?price=30,100')
        ->assertStatus(Response::HTTP_OK);

    $data = $response->json('data');
    $homePrices = array_column($data, 'price');
    $priceFilter = explode(',', request('price'));
    if ($priceFilter[0] && $priceFilter[1]) {
        foreach ($homePrices as $homePrice) {
            expect($homePrice)->toBeGreaterThanOrEqual($priceFilter[0])
                ->toBeLessThanOrEqual($priceFilter[1]);
        }
    } else if ($priceFilter[0]) {
        foreach ($homePrices as $homePrice) {
            expect($homePrice)->toBeGreaterThanOrEqual($priceFilter[0]);
        }
    }
});

