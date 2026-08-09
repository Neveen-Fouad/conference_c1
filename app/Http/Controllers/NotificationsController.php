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

        ], 201);
    }

    public function unread(int $clientId)
    {
        $notifications =
            $this->notificationService->getUnreadNotifications($clientId);

        return response()->json([
            'data' => $notifications,

        ]);
    }

    public function unreadCount(int $clientId)
    {
        $count =
            $this->notificationService->getUnreadCount($clientId);

        return response()->json([
            'unread_count' => $count,

        ]);
    }

    public function markAsRead(int $notificationId)
    {
        $notification =
            $this->notificationService->markAsRead($notificationId);

        return response()->json([
            'data' => $notification,
            'message' => 'Notification marked as read',

        ]);
    }

    public function markAllAsRead(int $clientId)
    {
        $updatedCount =
            $this->notificationService->markAllAsRead($clientId);

        return response()->json([
            'updated_count' => $updatedCount,
            'message' => 'All notifications marked as read',
        ]);
    }
}

