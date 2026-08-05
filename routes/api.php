<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\FlightController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/hotels/{hotel}', [HotelController::class, 'show']);

Route::get('/restaurants', [RestaurantController::class, 'index']);
Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show']);

Route::get('/flights', [FlightController::class, 'index']);
Route::get('/flights/{flight}', [FlightController::class, 'show']);