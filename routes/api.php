<?php

use App\Http\Controllers\NotificationsController;
use Illuminate\Support\Facades\Route;

Route::post('notifications', [NotificationsController::class, 'store']);

Route::get('notifications/{clientId}', [
    NotificationsController::class,
    'index',
]);
