<?php

namespace App\Services;

use App\Repositories\NotificationRepositoryInterface;

class NotificationService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository

    )
    {}
    public function createNotification(array $data)

    {
        return $this->notificationRepository->create($data);

    }
    public function getClientNotifications(int $clientId)
    {
        return $this->notificationRepository->getByClient($clientId);
    }


}
