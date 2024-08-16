<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\EmailVerificationController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\HomeFavouriteController;
use App\Http\Controllers\Api\V1\HotelController;
use App\Http\Controllers\Api\V1\HotelRoomsController;
use App\Http\Controllers\Api\V1\ResetPasswordController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\TourReservationController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::get('reviews/users/{user}', [ReviewController::class, 'UserReviews']);
Route::get('reviews/homes/{home}', [ReviewController::class, 'BookReviews']);
Route::middleware('auth:sanctum')->post('reviews', [ReviewController::class, 'store']);


//Route::post('homes', [HomeController::class, 'store']);

Route::apiResource('homes', HomeController::class);


Route::post('forgot-password', [ResetPasswordController::class, 'forgetPassword']);
Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);

Route::apiResource('tours', TourController::class);

Route::apiResource('users', UserController::class);

Route::get('users/favourites/{user}', [HomeFavouriteController::class, 'index']);
Route::post('users/favourites/toggle', [HomeFavouriteController::class, 'toggle']);


Route::post('email-verification', [EmailVerificationController::class, 'email_verification']);

Route::post('/tours/reserve', [TourReservationController::class, 'reserve']);
Route::get('/tours/{tour}/available-seats', [TourReservationController::class, 'getAvailableSeats']);
Route::get('/tours/{tour}/reserved-seats', [TourReservationController::class, 'getReservedSeats']);

Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/hotels/{hotel}', [HotelController::class, 'show']);
Route::post('/hotels', [HotelController::class, 'store'])->middleware(['auth:sanctum', 'hotel.owner']);


Route::get('/hotel-rooms', [HotelRoomsController::class, 'index']);
Route::get('/hotel-rooms/{id}', [HotelRoomsController::class, 'show']);
Route::get('/hotel-rooms/{id}/owner', [HotelRoomsController::class, 'getOwner']);
Route::get('/owner/{id}/hotel-rooms', [HotelRoomsController::class, 'getOwnerRooms']);
Route::get('/owner/{id}/hotel', [HotelController::class, 'hotelDetails']);

