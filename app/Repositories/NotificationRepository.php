<?php

namespace App\Repositories;

use App\Interfaces\NotificationRepositoryInterface;
use App\Models\Notification;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    public function __construct(Notification $notification)
    {
        parent::__construct($notification);
    }

    public function getByClient(int $clientId)
    {
        return $this->model
            ->where('client_id', $clientId)->latest()->get();
    }

    public function getUnreadByClient(int $clientId)
    {
        return $this->model
            ->where('client_id', $clientId)
            ->whereNull('read_at')
            ->latest()
            ->get();
    }

    public function getUnreadCount(int $clientId)
    {
        return $this->model
            ->where('client_id', $clientId)
            ->whereNull('read_at')
            ->count();
    }

    public function markAsRead(int $notificationId, int $clientId)
    {
        $notification = $this->model
            ->whereKey($notificationId)
            ->where('client_id', $clientId)
            ->firstOrFail();

        $notification->update([
            'read_at' => now(),
        ]);

        return $notification;
    }

    public function markAllAsRead(int $clientId)
    {
        return $this->model
            ->where('client_id', $clientId)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);
    }
}
