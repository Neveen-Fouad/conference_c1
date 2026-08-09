<?php

use App\Http\Controllers\TripController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymobWebhookController;
use App\Http\Controllers\RevenueController;






Route::get('/revenue/total', [RevenueController::class, 'totalRevenue']);




Route::post('/payments', [PaymentController::class, 'store']);

Route::get('/payments/client/{clientId}', [PaymentController::class, 'clientPayments']);
Route::get('/payments/{paymentId}', [PaymentController::class, 'show']);
Route::post('/payments', [PaymentController::class, 'store']);
Route::post('/paymob/webhook', [PaymobWebhookController::class, 'handle']);
use App\Http\Controllers\NotificationsController;

Route::post('/notifications', [NotificationsController::class, 'store']);

Route::get('/notifications/client/{clientId}', [NotificationsController::class, 'index']);

Route::get('/notifications/client/{clientId}/unread', [NotificationsController::class, 'unread']);

Route::get('/notifications/client/{clientId}/unread-count', [NotificationsController::class, 'unreadCount']);

Route::patch('/notifications/{notificationId}/read', [NotificationsController::class, 'markAsRead']);

Route::patch('/notifications/client/{clientId}/read-all', [NotificationsController::class, 'markAllAsRead']);


