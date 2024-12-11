<?php
declare(strict_types=1);

use App\Models\Hotel;
use App\Models\HotelRooms;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolesAndPermissionSeeder;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->seed(RolesAndPermissionSeeder::class);
    $this->seed(DatabaseSeeder::class);
});

it('gets the correct status code for unauthenticated user', function (): void {
    getJson('api/hotel-rooms')->assertStatus(status: Response::HTTP_UNAUTHORIZED);
});

it('gets the correct status code for authenticated users', function (): void {

    $user = User::factory()->create(['preferred_currency' => 'SYP']);


    Sanctum::actingAs($user, ['*']);
    getJson('api/hotel-rooms')->assertStatus(Response::HTTP_OK);
});


it('gets the correct status code based on user role', function (): void {
    Sanctum::actingAs(
        User::factory()->create(),
    );

    $amenities = [
        'air_conditioner' => true,
        'washing_machine' => fake()->boolean(),
        'dryer' => fake()->boolean(),
        'breakfast' => fake()->boolean(),
        'free_wifi' => true,
        'sea_view' => fake()->boolean(),
        'mountain_view' => fake()->boolean(),
        'city_view' => fake()->boolean(),
        'terrace' => fake()->boolean(),
        'balcony' => fake()->boolean(),
        'heating' => fake()->boolean(),
        'coffee_machine' => fake()->boolean(),
        'free_parking' => fake()->boolean(),
        'swimming_pools' => fake()->boolean(),
        'restaurant' => fake()->boolean(),
        'tv' => fake()->boolean(80),
        'reception_service' => fake()->boolean(),
        'cleaning_service' => fake()->boolean(),
        'garden' => fake()->boolean(),

    ];

    $roomData = [
        'price' => fake()->numberBetween(100, 10000),
        'area' => fake()->numberBetween(40, 120),
        'bathrooms_no' => fake()->numberBetween(1, 3),
        'bedrooms_no' => fake()->numberBetween(1, 3),
        'hotel_id' => Hotel::factory()->create()->id,
        'available_from' => fake()->dateTimeBetween('now', '+1 day')->format('Y-m-d'),
        'available_until' => fake()->dateTimeBetween('+2 months', '+3 months')->format('Y-m-d'),
        'capacity' => fake()->randomDigitNotNull(),
        'beds' => [
            [
                'bed_type' => 'double bed'
            ],
            [
                'bed_type' => 'single bed'
            ],

        ],
        'amenities' => $amenities,
    ];
    
    // Attempt to create a room as an unauthorized user
    $this->post('api/hotel-rooms', $roomData)->assertStatus(Response::HTTP_FORBIDDEN);

    // Simulate a user with the correct role (hotel owner)
    $user = Hotel::factory()->create()->owner->user;
    $user->assignRole('Hotel Owner');
    Sanctum::actingAs($user);

    // Create a room as an authorized user
    $response = $this->postJson('api/hotel-rooms', $roomData)
        ->assertStatus(Response::HTTP_CREATED)
        ->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'price',
                'discount_price',
                'currency',
                'area',
                'bathrooms_no',
                'bedrooms_no',
                'room_images',
                'hotel_id',
                'available_from',
                'available_until',
                'is_available',
                'capacity',
                'beds' => [
                    '*' => ['id', 'bed_type'],
                ],
                'amenities' => [
                    'id',
                    array_keys($amenities)
                ]
            ]
        ]);
    $createdRoom = Hotel::find($response['data']['id']);

    expect($createdRoom)->not()->toBeNull();
    $createdBeds = $createdRoom->roomBeds;
    expect($createdBeds)->toHaveCount(2);
    expect($createdBeds->pluck('bed_type'))->toContain([
        'single bed',
        'double bed'
    ]);
    $createdAmenities = $createdRoom->AccommodationAmenities;
    expect($createdAmenities)->not()->toBeNull();
    expect($createdAmenities['air_conditioner'])->toBe(true);
    expect($createdAmenities['free_wifi'])->toBe(true);

});


it('filters the rooms according to its price', function (): void {

    $user = User::factory()->create(['preferred_currency' => 'SYP']);
    Sanctum::actingAs($user);

    $response = $this->getJson('api/hotel-rooms?price=300,500')
        ->assertStatus(Response::HTTP_OK);

    $data = $response->json('data');
    $roomPrices = array_column($data, 'price');
    $priceFilter = explode(',', request('price'));
    if ($priceFilter[0] && $priceFilter[1]) {
        //room prices should be in between price filter values
        foreach ($roomPrices as $roomPrice) {
            expect($roomPrice)->toBeGreaterThanOrEqual($priceFilter[0])
                ->toBeLessThanOrEqual($priceFilter[1]);
        }
    } else if ($priceFilter[0]) {
        foreach ($roomPrices as $roomPrice) {
            expect($roomPrice)->toBeGreaterThanOrEqual($priceFilter[0]);
        }
    }

});
