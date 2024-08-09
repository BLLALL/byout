<?php

use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\UserController;
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


Route::get('reviews/users/{user}', [ReviewController::class, 'UserReviews']);
Route::get('reviews/homes/{home}', [ReviewController::class, 'BookReviews']);
Route::middleware('auth:sanctum')->post('reviews', [ReviewController::class, 'store']);

Route::middleware('auth:sanctum')->post('homes', [HomeController::class, 'store']);

Route::apiResource('tours', TourController::class);


Route::middleware('auth:sanctum')->get('users/{user}', [UserController::class, 'show'])->name('users.show');
