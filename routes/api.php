<?php

use App\Http\Controllers\FavouritesController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminInterestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\TripController;


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

//trip management 
Route::prefix('admin/trips')->group(function() {
    
    Route::get('/', [TripController::class, 'index']);

    Route::patch('/{id}', [TripController::class, 'update']);

    Route::delete('/{id}', [TripController::class, 'destroy']);

    Route::get('/statistics', [TripController::class, 'statistics']);
});



use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



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
});// review endpoint (user)
Route::middleware(['auth:api','IsActive','VerifiedEmail'])->group(function (){
    Route::get('/reviews' , [ReviewController::class,'Index']);
    Route::get('/reviews/my' , [ReviewController::class,'getMyReviews']);
    Route::get('/reviews/{review_id}', [ReviewController::class,'show']);
    Route::post('/reviews' , [ReviewController::class,'store']);
    Route::put('/reviews/{review_id}' ,[ReviewController::class,'update']);
    Route::delete('/reviews/{review_id}',[ReviewController::class, 'destroy']);
});
// review endpoint (admin)
Route::middleware(['auth:api','isAdmin','IsActive', 'VerifiedEmail'])->prefix('admin')->group(function(){
    Route::get('/reviews',[ReviewController::class,'Index']);
    Route::post('/reviews/{review_id}/approve',[ReviewController::class,'approve']);
    Route::post('/reviews/{review_id}/reject',[ReviewController::class,'reject']);
});
// favourite endpoint (user)
Route::middleware(['auth:api','IsActive','VerifiedEmail'])->group(function(){
    Route::get('/favourites',[FavouritesController::class,'index']);
    Route::post('/favourites',[FavouritesController::class,'store']);
    Route::delete('/favourites/{favourite_id}',[FavouritesController::class,'destroy']);
});
Route::middleware(['auth:api','isAdmin','IsActive'])->prefix('admin')->group(function(){
    Route::get('/interests',[AdminInterestController::class,'index']);
    Route::post('/interests',[AdminInterestController::class,'store']);
    Route::put('/interests/{interest_id}',[AdminInterestController::class,'update']);
    Route::delete('/interests/{interest_id}',[AdminInterestController::class,'destroy']);
});