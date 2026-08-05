<?php

use App\Http\Controllers\ExploreController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/explore', [ExploreController::class, 'index'])->name('explore.index');
Route::get('/api/destination-data', [ExploreController::class, 'destinationData']);
// Route::get('/api/attractions/more', [ExploreController::class, '']);