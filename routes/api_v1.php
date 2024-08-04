<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewController;
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

Route::apiResource('homes', HomeController::class);

Route::get('reviews/users/{user}', [ReviewController::class, 'UserReviews']);
Route::get('reviews/homes/{home}', [ReviewController::class, 'BookReviews']);

Route::middleware('auth:sanctum')->post('reviews', [ReviewController::class, 'store']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
