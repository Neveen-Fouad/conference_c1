<?php

use App\Http\Controllers\TripController;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymobWebhookController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::prefix('admin')->middleware('isAdmin')->group(function () {  // usermanagement


    Route::get('/users/{id}', [UserController::class, 'show']);

    Route::patch('/users/{id}/status', [UserController::class, 'changeStatus']);

    Route::post('/admins', [UserController::class, 'storeAdmin']);

    Route::patch('/admins/{id}', [UserController::class, 'updateAdmin']);

    Route::delete('/admins/{id}', [UserController::class, 'destroyAdmin']);
    Route::get('/statistics', [UserController::class, 'statistics']);
});
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

//Profile

Route::middleware('auth:api')->group(function (){
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::patch('/profile', [ProfileController::class, 'updateProfile']);
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword']);
});

//User Dashboard

Route::prefix('dashboard')->middleware('auth:api')->group(function () {
    Route::get('/saved-trips', [DashboardController::class, 'getSavedTrips']);
    Route::get('/favorite-destinations', [DashboardController::class, 'getFavouriteDestinations']);
    Route::get('/booking-history', [DashboardController::class, 'getBookingHistory']);
    Route::get('/profile-settings', [DashboardController::class, 'getProfileSettings']);
    Route::patch('/profile-settings', [DashboardController::class, 'updateProfileSettings']);
    Route::get('/statistics', [DashboardController::class, 'getStatistics']);
});
//website settings
Route::prefix('admin/settings')->middleware('isAdmin')->group(function () {
     Route::get('/', [SettingController::class, 'index']);

    Route::post('/', [SettingController::class, 'storeSettings']);
    Route::patch('/{id}', [SettingController::class, 'updateSettings']);

   
    
});

//complaint
Route::post('/contact', [ComplaintController::class, 'store']);

Route::prefix('admin/contact-messages')->middleware('isAdmin')->group(function () {

    Route::get('/', [ComplaintController::class, 'index']);

    Route::delete('/{id}', [ComplaintController::class, 'destroy']);

    Route::patch('/{id}/status', [ComplaintController::class, 'changeStatus']);

});
Route::apiResource('/trips',TripController::class);
Route::get('/user/trips/{userId}', [TripController::class, 'getTripsByUserId']);
Route::post('/payments', [PaymentController::class, 'store']);
Route::prefix('admin/trips')->middleware('isAdmin')->group(function() {
    
    Route::get('/', [TripController::class, 'index']);

    Route::patch('/{id}', [TripController::class, 'update']);

    Route::delete('/{id}', [TripController::class, 'destroy']);

    Route::get('/statistics', [TripController::class, 'statistics']);
});

Route::get('/payments/client/{clientId}', [PaymentController::class, 'clientPayments']);
Route::get('/payments/{paymentId}', [PaymentController::class, 'show']);
Route::post('/payments', [PaymentController::class, 'store']);
Route::get('/payments/client/{clientId}', [PaymentController::class, 'clientPayments']);
Route::get('/payments/{paymentId}', [PaymentController::class, 'show']);
Route::post('/paymob/webhook', [PaymobWebhookController::class, 'handle']);


