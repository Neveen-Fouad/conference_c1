<?php

use App\Http\Controllers\TripController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ComplaintController;


Route::prefix('admin')->group(function () {  // usermanagement 

    Route::get('/users', [UserController::class, 'index']);

    Route::get('/users/{id}', [UserController::class, 'show']);

    Route::patch('/users/{id}/status', [UserController::class, 'changeStatus']);

    Route::post('/admins', [UserController::class, 'storeAdmin']);

    Route::patch('/admins/{id}', [UserController::class, 'updateAdmin']);

    Route::delete('/admins/{id}', [UserController::class, 'destroyAdmin']);

});

//website settings
Route::prefix('admin/settings')->group(function (){
     Route::get('/', [SettingController::class, 'index']);

    Route::post('/logo', [SettingController::class, 'setLogo']);
    Route::patch('/logo', [SettingController::class, 'updateLogo']);

    Route::post('/site-name', [SettingController::class, 'setSiteName']);
    Route::patch('/site-name', [SettingController::class, 'updateSiteName']);

    Route::post('/contact-info', [SettingController::class, 'setContactInfo']);
    Route::patch('/contact-info', [SettingController::class, 'updateContactInfo']);

    Route::post('/social-links', [SettingController::class, 'setSocialLinks']);
    Route::patch('/social-links', [SettingController::class, 'updateSocialLinks']);

    Route::post('/banner', [SettingController::class, 'setBanner']);
    
    Route::patch('/banner', [SettingController::class, 'updateBanner']);

});

//complaint
Route::post('/contact', [ComplaintController::class, 'store']);

Route::prefix('admin/contact-messages')->group(function () {

    Route::get('/', [ComplaintController::class, 'index']);

    Route::delete('/{id}', [ComplaintController::class, 'destroy']);

    Route::patch('/{id}/status', [ComplaintController::class, 'changeStatus']);

});
Route::apiResource('/trips',TripController::class);
Route::get('/user/trips/{userId}', [TripController::class, 'getTripsByUserId']);
