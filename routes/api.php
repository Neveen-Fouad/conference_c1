<?php

use App\Http\Controllers\FavouritesController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
    Route::post('/reviews/{review_id}/approve',[ReviewController::class,'approveReview']);
    Route::post('/reviews/{review_id}/reject',[ReviewController::class,'rejectReview']);
});
// favourite endpoint (user)
Route::middleware('auth:api')->group(function(){
    Route::get('/favourites',[FavouritesController::class,'index']);
    Route::post('/favourites',[FavouritesController::class,'store']);
    Route::delete('/favourites/{favourite_id}',[FavouritesController::class,'destroy']);
});