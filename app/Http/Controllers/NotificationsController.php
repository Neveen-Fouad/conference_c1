<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index(Request $request, int $clientId)
    {
        $this->authorizeClient($request, $clientId);
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

    public function unread(Request $request, int $clientId)
    {
        $this->authorizeClient($request, $clientId);
        $notifications =
            $this->notificationService->getUnreadNotifications($clientId);

        return response()->json([
            'data' => $notifications,

        ]);
    }

    public function unreadCount(Request $request, int $clientId)
    {
        $this->authorizeClient($request, $clientId);
        $count =
            $this->notificationService->getUnreadCount($clientId);

        return response()->json([
            'unread_count' => $count,

        ]);
    }

    public function markAsRead(Request $request, int $notificationId)
    {
        $notification =
            $this->notificationService->markAsRead($notificationId, $this->clientId($request));

        return response()->json([
            'data' => $notification,
            'message' => 'Notification marked as read',

        ]);
    }

    public function markAllAsRead(Request $request, int $clientId)
    {
        $this->authorizeClient($request, $clientId);
        $updatedCount =
            $this->notificationService->markAllAsRead($clientId);

        return response()->json([
            'updated_count' => $updatedCount,
            'message' => 'All notifications marked as read',
        ]);
    }

    private function authorizeClient(Request $request, int $clientId): void
    {
        abort_unless($clientId === $this->clientId($request), 403, 'Unauthorized action.');
    }

    private function clientId(Request $request): int
    {
        $clientId = $request->user()?->client?->id;
        abort_if($clientId === null, 403, 'A client profile is required.');

        return $clientId;
    }
}
