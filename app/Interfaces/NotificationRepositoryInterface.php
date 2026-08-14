<?php

namespace App\Interfaces;

interface NotificationRepositoryInterface extends BaseRepositoryInterface
{
    public function getByClient(int $clientId);

    public function getUnreadByClient(int $clientId);

    public function getUnreadCount(int $clientId);

    public function markAsRead(int $notificationId, int $clientId);

    public function markAllAsRead(int $clientId);
}
