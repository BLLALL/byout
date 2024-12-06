<?php
declare(strict_types=1);

use App\Models\Hotel;
use App\Models\User;
use Database\Seeders\RolesAndPermissionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->seed(RolesAndPermissionSeeder::class);
    $this->seed(\Database\Seeders\DatabaseSeeder::class);
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

    Storage::fake('avatars');

    // Create fake image files
    $roomImages = [
        UploadedFile::fake()->image('image1.jpg'),
        UploadedFile::fake()->image('image2.jpg'),
    ];

    $room = [
        'price' => fake()->numberBetween(100, 10000),
        'area' => fake()->numberBetween(40, 120),
        'bathrooms_no' => fake()->numberBetween(1, 3),
        'bedrooms_no' => fake()->numberBetween(1, 3),
//        'room_images' => $roomImages,
        'hotel_id' => Hotel::factory()->create()->id,
        'available_from' => fake()->dateTimeBetween('now', '+1 day')->format('Y-m-d'),
        'available_until' => fake()->dateTimeBetween('+2 months', '+3 months')->format('Y-m-d'),
    ];


    // Attempt to create a room as an unauthorized user
    $this->post('api/hotel-rooms', $room)->assertStatus(Response::HTTP_FORBIDDEN);

    // Simulate a user with the correct role (hotel owner)
    $user = Hotel::factory()->create()->owner->user;
    $user->assignRole('Hotel Owner');
    Sanctum::actingAs($user);

    // Create a room as an authorized user
    $response = $this->postJson('api/hotel-rooms', $room)->assertStatus(Response::HTTP_CREATED);

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
