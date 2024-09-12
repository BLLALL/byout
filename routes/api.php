<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\ChaletController;
use App\Http\Controllers\Api\V1\DriverController;
use App\Http\Controllers\Api\V1\EmailVerificationController;
use App\Http\Controllers\Api\V1\ExchangeRateController;
use App\Http\Controllers\Api\V1\FavouriteController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\HotelController;
use App\Http\Controllers\Api\V1\HotelRoomsController;
use App\Http\Controllers\Api\V1\OwnerController;
use App\Http\Controllers\Api\V1\ResetPasswordController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\TourReservationController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\BusController;
use App\Http\Controllers\Api\V1\VehicleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\RentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// http://localhost:8000/api/
// univseral resource locator
// houses...
// users

Route::post('/login', [AuthController::class, 'login']);

Route::post('login/google', [AuthController::class, 'google']);
Route::post('login/google/redirect', [AuthController::class, 'googleRedirect']);

Route::post('/register/user', [AuthController::class, 'register']);
Route::post('/register/owner', [AuthController::class, 'registerOwner']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);




Route::post('email-verification', [EmailVerificationController::class, 'email_verification']);


Route::post('forgot-password', [ResetPasswordController::class, 'forgetPassword']);
Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);

Route::get('token/check', [AuthController::class, 'checkToken']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('token/refresh', [AuthController::class, 'refreshToken']);

    Route::apiResource('tours', TourController::class);
    Route::apiResource('homes', HomeController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('chalets', ChaletController::class);
    Route::apiResource('hotels', HotelController::class);

    Route::post('rent', [RentController::class, 'rent']);
    Route::get('owners/{owner}/report', [OwnerController::class, 'getOwnerFinancialReport']);
    Route::get('owners/{owner}/period-report', [OwnerController::class, 'getOwnerFincialReportByPeriod']);

    Route::get('reviews/users/{user}', [ReviewController::class, 'UserReviews']);
    Route::get('reviews/homes/{home}', [ReviewController::class, 'HomeReviews']);
    Route::get('reviews/hotels/{hotel}', [ReviewController::class, 'HotelReviews']);
    Route::get('reviews/chalets/{chalet}', [ReviewController::class, 'ChaletReviews']);
    Route::post('reviews', [ReviewController::class, 'store']);


    Route::post('/tours/reserve', [TourReservationController::class, 'reserve']);
    Route::get('/tours/{tour}/available-seats', [TourReservationController::class, 'getAvailableSeats']);
    Route::get('/tours/{id}/reserved-seats', [TourReservationController::class, 'getReservedSeats']);

    Route::apiResource('buses', BusController::class);
    Route::get('available-buses', [BusController::class, 'getAvailableBuses']);
    Route::get('in-use-buses', [BusController::class, 'getInUseBuses']);


    Route::apiResource('drivers', DriverController::class);
    Route::apiResource('vehicles', VehicleController::class);
    Route::post('tours/schedule', [TourController::class, 'schedule']);
    Route::get('users/favourites/{user}', [FavouriteController::class, 'index']);
    Route::post('users/favourites/toggle', [FavouriteController::class, 'toggle']);

    Route::get('home-owners', [OwnerController::class, 'homeOwners']);
    Route::get('/owner/{id}/hotel-rooms', [HotelRoomsController::class, 'getOwnerRooms']);
    Route::get('/owner/{id}/hotel', [HotelController::class, 'hotelDetails']);

    Route::get('/hotel-rooms', [HotelRoomsController::class, 'index']);
    Route::get('/hotel-rooms/{id}', [HotelRoomsController::class, 'show']);
    Route::get('/hotel-rooms/{id}/owner', [HotelRoomsController::class, 'getOwner']);
    Route::post('/hotel-rooms', [HotelRoomsController::class, 'store']);
    Route::patch('/hotel-rooms/{id}', [HotelRoomsController::class, 'update']);
    Route::delete('/hotel-rooms/{id}', [HotelRoomsController::class, 'destroy']);
});

Route::get('rates', [ExchangeRateController::class, 'getRates']);
