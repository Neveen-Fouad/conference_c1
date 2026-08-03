<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {
    }

    public function index(int $clientId)
    {
        $notifications =
            $this->notificationService->getClientNotifications($clientId);

        return response()->json([
            'data' => $notifications,
            'success' => true,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'type' => 'required|string',
            'description' => 'required|string',
        ]);

        $notification =
            $this->notificationService->createNotification($validatedData);

        return response()->json([
            'data' => $notification,
            'message' => 'Notification created successfully',
            'success' => true,
        ], 201);
    }
}
