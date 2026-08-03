<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\InterestsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{country}', [CountryController::class, 'show']);
Route::get('/explore', [ExploreController::class, 'index'])->name('explore.index');
Route::get('/destination-data', [ExploreController::class, 'destinationData']);

Route::get('/client/interests', [InterestsController::class, 'clientInterests']);
Route::put('/client/interests', [InterestsController::class, 'updateClientInterests']);

Route::get('/interests', [InterestsController::class, 'index']);