<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\EmailVerificationController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\TourController;
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
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


Route::get('reviews/users/{user}', [ReviewController::class, 'UserReviews']);
Route::get('reviews/homes/{home}', [ReviewController::class, 'BookReviews']);
Route::middleware('auth:sanctum')->post('reviews', [ReviewController::class, 'store']);


//Route::post('homes', [HomeController::class, 'store']);

Route::middleware('auth:sanctum')->apiResource('homes', HomeController::class);

Route::apiResource('tours', TourController::class);

Route::apiResource('users', UserController::class);

Route::post('email-verification', [EmailVerificationController::class, 'email_verification']);
