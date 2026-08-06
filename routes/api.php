<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\InterestsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HotelBookingsController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\SearchController;



Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
        ->middleware('auth:api')
        ->name('verification.send');
    Route::middleware('auth:api')->group(function (){
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});


Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{country}', [CountryController::class, 'show']);
Route::get('/explore', [ExploreController::class, 'index'])->name('explore.index');
Route::get('/destination-data', [ExploreController::class, 'destinationData']);

Route::get('/client/interests', [InterestsController::class, 'clientInterests']);
Route::put('/client/interests', [InterestsController::class, 'updateClientInterests']);

Route::get('/interests', [InterestsController::class, 'index']);
Route::get('/hotels/search', [HotelBookingsController::class, 'search']);

Route::middleware('auth:api')->group(function () {
    Route::post('/hotels/bookings', [HotelBookingsController::class, 'store']);
});

Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/hotels', [HotelController::class, 'show']);

Route::get('/hotels/search', [SearchController::class, 'searchHotels']);

Route::get('/restaurants', [RestaurantController::class, 'index']);
Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show']);

Route::get('/flights', [FlightController::class, 'index']);
Route::get('/flights/{flight}', [FlightController::class, 'show']);