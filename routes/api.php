<?php

use App\Http\Controllers\AdminInterestController;
use App\Http\Controllers\AiTripController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingListController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardReportController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\FavouritesController;
use App\Http\Controllers\FlightBookingController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\HotelBookingsController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\InterestsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymobWebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransportationController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TripMemoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;


// payments
Route::middleware('auth:api')->group(function () {
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments/client/{clientId}', [PaymentController::class, 'clientPayments']);
    Route::get('/payments/{paymentId}', [PaymentController::class, 'show']);
});

// global bookings
Route::middleware('auth:api')->group(function () {
    Route::get('/bookings', [BookingListController::class, 'all']);
    Route::get('/bookings/hotels', [BookingListController::class, 'hotels']);
    Route::get('/bookings/flights', [BookingListController::class, 'flights']);
});

// authentication
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:40,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:5,1')
        ->name('password.reset');


Route::get('/email/verify/{id}/{hash}', function (Request $request) {
    if (! $request->hasValidSignature()) {
        return response()->json(['message' => 'This verification link is invalid or has expired.'], 403);
    }

    $user = User::find($request->route('id'));

    if (! $user) {
        return response()->json(['message' => 'User not found.'], 404);
    }

    if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Invalid verification link.'], 403);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Your email is already verified.']);
    }

    $user->markEmailAsVerified();

    return response()->json(['message' => 'Your email is verified. You can now sign in.']);
})->middleware(['signed'])->name('verification.verify');
    
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:60,1')
        ->name('verification.send');
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
    Route::post('/refresh', [AuthController::class, 'refresh'])
        ->middleware('auth:api');
});

// profile
Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::patch('/profile', [ProfileController::class, 'updateProfile']);
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword']);
});

// user dashboard
Route::prefix('dashboard')->middleware('auth:api')->group(function () {
    Route::get('/saved-trips', [DashboardController::class, 'getSavedTrips']);
    Route::get('/favorite-destinations', [DashboardController::class, 'getFavouriteDestinations']);
    Route::get('/booking-history', [DashboardController::class, 'getBookingHistory']);
    Route::get('/profile-settings', [DashboardController::class, 'getProfileSettings']);
    Route::patch('/profile-settings', [DashboardController::class, 'updateProfileSettings']);
    Route::get('/statistics', [DashboardController::class, 'getStatistics']);
});

// favourite endpoint (user)
Route::middleware(['auth:api', 'IsActive', 'VerifiedEmail'])->group(function () {
    Route::get('/favourites', [FavouritesController::class, 'index']);
    Route::post('/favourites', [FavouritesController::class, 'store']);
    Route::delete('/favourites/{favourite_id}', [FavouritesController::class, 'destroy']);
});

// admin interests
Route::middleware(['auth:api', 'isAdmin'])->prefix('admin')->group(function () {
    Route::get('/interests', [AdminInterestController::class, 'index']);
    Route::post('/interests', [AdminInterestController::class, 'store']);
    Route::put('/interests/{interest_id}', [AdminInterestController::class, 'update']);
    Route::delete('/interests/{interest_id}', [AdminInterestController::class, 'destroy']);
});

// revenue
Route::get('/revenue/total', [RevenueController::class, 'totalRevenue'])
    ->middleware(['auth:api', 'isAdmin']);

// notifications
Route::post('/notifications', [NotificationsController::class, 'store'])
    ->middleware(['auth:api', 'isAdmin']);
Route::middleware('auth:api')->group(function () {
    Route::get('/notifications/client/{clientId}', [NotificationsController::class, 'index']);
    Route::get('/notifications/client/{clientId}/unread', [NotificationsController::class, 'unread']);
    Route::get('/notifications/client/{clientId}/unread-count', [NotificationsController::class, 'unreadCount']);
    Route::patch('/notifications/{notificationId}/read', [NotificationsController::class, 'markAsRead']);
    Route::patch('/notifications/client/{clientId}/read-all', [NotificationsController::class, 'markAllAsRead']);
});

// explore
Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{country}', [CountryController::class, 'show']);
Route::get('/explore', [ExploreController::class, 'index'])->name('explore.index');
Route::get('/destination-data', [ExploreController::class, 'destinationData']);

// interests
Route::get('/interests', [InterestsController::class, 'index']);

// hotels details
Route::get('/hotels/details', [HotelController::class, 'show']);

// trips and memories

Route::get('/trips/pre-made', [TripController::class, 'getPreMadeTrips']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('/trips', TripController::class);
    Route::get('/user/trips/{userId}', [TripController::class, 'getTripsByUserId']);
    Route::get('/trips/{trip}/tripDays', [TripController::class, 'getTripDays']);

    Route::post('/ai/trips', [AiTripController::class, 'generateTrip']);

    Route::post('/trips/{trip}/memories', [TripMemoryController::class, 'store']);
    Route::get('/trips/{trip}/memories', [TripMemoryController::class, 'index']);
    Route::delete('/trips/{trip}/memories/{memory}', [TripMemoryController::class, 'destroy']);
    
    Route::post('/trips/{trip}/book', [TripController::class, 'book']);
});

// admin dashboard
Route::middleware(['auth:api', 'isAdmin'])->group(function () {
    Route::get('/admin/dashboard/export-pdf', [DashboardReportController::class, 'exportPdf']);
    Route::get('/admin/dashboard/statistics', [DashboardReportController::class, 'statistics']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/hotels/bookings', [HotelBookingsController::class, 'store']);
    Route::get('/client/interests', [InterestsController::class, 'clientInterests']);
    Route::put('/client/interests', [InterestsController::class, 'updateClientInterests']);
});

// hotels endpoints
Route::get('/hotels/search', [SearchController::class, 'searchHotels']);

// restaurants endpoint
Route::get('/restaurants', [RestaurantController::class, 'index']);
Route::get('/restaurants/details', [RestaurantController::class, 'show']);

// flights endpoint
Route::get('/flights/search-airport', [FlightController::class, 'searchAirports']);
Route::get('/flights/search', [FlightController::class, 'listFlights']);
Route::post('/flights/book', [FlightBookingController::class, 'bookFlight'])->middleware('auth:api');

// review endpoint (user)
Route::middleware('auth:api')->group(function () {
    Route::get('/reviews', [ReviewController::class, 'UserIndex']);
    Route::get('/reviews/my', [ReviewController::class, 'getMyReviews']);
    Route::get('/reviews/{review_id}', [ReviewController::class, 'show']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::post('/reviews/{review_id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review_id}', [ReviewController::class, 'destroy']);
});

// review endpoint (admin)
Route::middleware(['auth:api', 'isAdmin'])->prefix('admin')->group(function () {
    Route::get('/reviews', [ReviewController::class, 'AdminIndex']);
    Route::post('/reviews/{review_id}/approve', [ReviewController::class, 'approve']);
    Route::post('/reviews/{review_id}/reject', [ReviewController::class, 'reject']);
});

// usermanagement
Route::prefix('admin')->middleware(['isAdmin', 'auth:api'])->group(function () {

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::patch('/users/{id}/status', [UserController::class, 'changeStatus']);
    Route::post('/admins', [UserController::class, 'storeAdmin']);
    Route::patch('/admins/{id}', [UserController::class, 'updateAdmin']);
    Route::delete('/admins/{id}', [UserController::class, 'destroyAdmin']);
    Route::get('/statistics', [UserController::class, 'statistics']);
});

// website settings
Route::prefix('admin/settings')->middleware(['isAdmin', 'auth:api'])->group(function () {
    Route::get('/', [SettingController::class, 'index']);
    Route::post('/', [SettingController::class, 'storeSettings']);
    Route::patch('/{id}', [SettingController::class, 'UpdateSettings']);
});

// complaint
Route::post('/contact', [ComplaintController::class, 'store'])->middleware('throttle:10,1');

// admin contact messages
Route::prefix('admin/contact-messages')->middleware(['isAdmin', 'auth:api'])->group(function () {
    Route::get('/', [ComplaintController::class, 'index']);
    Route::get('/{id}', [ComplaintController::class, 'show']);
    Route::delete('/{id}', [ComplaintController::class, 'destroy']);
    Route::patch('/{id}/status', [ComplaintController::class, 'changeStatus']);
});

// admin trips
Route::prefix('admin/trips')->middleware(['isAdmin', 'auth:api'])->group(function () {
    Route::get('/statistics', [TripController::class, 'statistics']);
});

Route::post('/paymob/webhook', [PaymobWebhookController::class, 'handle']);

// chatbot
Route::middleware('auth:api')
    ->prefix('chat')
    ->group(function () {
        Route::get('/conversations', [
            ChatbotController::class,
            'index',
        ]);
        Route::get('/conversations/{conversationId}', [
            ChatbotController::class,
            'show',
        ]);
        Route::post('/messages', [
            ChatbotController::class,
            'sendMessage',
        ]);
    });

Route::middleware('auth:api')->group(function () {
    Route::post('/transportation/tips', [TransportationController::class, 'tips']);
});
// admin listbooking
Route::middleware(['auth:api', 'isAdmin'])->prefix('admin/bookings')->group(function () {
    Route::get('/{clientId}', [BookingListController::class, 'adminAll']);
    Route::get('/{clientId}/hotels', [BookingListController::class, 'adminHotels']);
    Route::get('/{clientId}/flights', [BookingListController::class, 'adminFlights']);
});
