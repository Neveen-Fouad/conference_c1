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
    public function unread(int $clientId, int $notificationId){
        $notification = $this->notificationService->getUnreadNotifications($clientId);
        return response()->json([
            'data' => $notification,
            'success' => true,

        ],201);

        }
        public function unreadCount(int $clientId)
        {
            $count=$this->notificationService->getUnreadNotifications($clientId);
            return response()->json([
                "unreadCount" => $count,
                'success' => true,

            ]);

        }
        public function markAsRead(int $notificationId){
        $this->notificationService->markAsRead($notificationId);
        return response()->json([
            'success' => true,
            "message" => "Notification marked as read",


        ]);

        }
        public function markAllAsRead(int $clientId){
        $this->notificationService->markAllAsRead($clientId);
        return response()->json([
            'success' => true,
            "message" => "Notification marked as read",
            "updated_count" =>"updated_count",
        ]);
        }




}

