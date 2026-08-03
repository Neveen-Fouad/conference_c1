<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationsController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/notifications', [NotificationsController::class, 'store',
]);

Route::get('/notifications/client/{clientId}', [NotificationsController::class, 'index',]);

Route::get('/notifications/client/{clientId}/unread', [NotificationsController::class, 'unread',]);

Route::get('/notifications/client/{clientId}/unread-count', [NotificationsController::class, 'unreadCount',]);

Route::patch('/notifications/{notificationId}/read', [NotificationsController::class, 'markAsRead',]);

Route::patch('/notifications/client/{clientId}/read-all', [NotificationsController::class, 'markAllAsRead',]);
