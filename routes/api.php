<?php

use App\Http\Controllers\TripController;
use GuzzleHttp\Middleware;
use App\Http\Controllers\FavouritesController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\InterestsController;
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

use App\Http\Controllers\HotelBookingsController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FlightBookingController;


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



Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{country}', [CountryController::class, 'show']);
Route::get('/explore', [ExploreController::class, 'index'])->name('explore.index');
Route::get('/destination-data', [ExploreController::class, 'destinationData']);

Route::get('/client/interests', [InterestsController::class, 'clientInterests']); // those are commented out in the controller
Route::put('/client/interests', [InterestsController::class, 'updateClientInterests']); // those are commented out in the controller

Route::get('/interests', [InterestsController::class, 'index']);
// Route::get('/hotels/search', [HotelBookingsController::class, 'search']);

Route::middleware('auth:api')->group(function () {
    Route::post('/hotels/bookings', [HotelBookingsController::class, 'store']);
});

Route::get('/hotels', [HotelController::class, 'index']); // there is no controller method for this route yet
Route::get('/hotels/details', [HotelController::class, 'show']);

Route::get('/hotels/search', [SearchController::class, 'searchHotels']);

// restaurants endpoint
Route::get('/restaurants', [RestaurantController::class, 'index']);
Route::get('/restaurants/details', [RestaurantController::class, 'show']);

// flights endpoint
Route::get('/flights/search-airport', [FlightController::class, 'searchAirports']);
Route::get('/flights/search', [FlightController::class, 'listFlights']);
Route::post('/flights/book', [FlightBookingController::class, 'bookFlight'])->middleware('auth:api');

// review endpoint (user)
Route::middleware('auth:api')->group(function (){
    Route::get('/reviews' , [ReviewController::class,'UserIndex']);
    Route::get('/reviews/my' , [ReviewController::class,'getMyReviews']);
    Route::get('/reviews/{review_id}', [ReviewController::class,'show']);
    Route::post('/reviews' , [ReviewController::class,'store']);
    Route::put('/reviews/{review_id}' ,[ReviewController::class,'update']);
    Route::delete('/reviews/{review_id}',[ReviewController::class, 'destroy']);
});
// review endpoint (admin)
Route::middleware(['auth:api','isAdmin'])->prefix('admin')->group(function(){
    Route::get('/reviews',[ReviewController::class,'AdminIndex']);
    Route::post('/reviews/{review_id}/approve',[ReviewController::class,'approve']);
    Route::post('/reviews/{review_id}/reject',[ReviewController::class,'reject']);
});

// favorite endpoint (user)
Route::middleware('auth:api')->group(function(){
    Route::get('/favourites',[FavouritesController::class,'index']);
    Route::post('/favourites',[FavouritesController::class,'store']);
    Route::delete('/favourites/{favourite_id}',[FavouritesController::class,'destroy']);
});
