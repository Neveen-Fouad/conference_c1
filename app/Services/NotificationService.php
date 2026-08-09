<?php

namespace App\Services;

use App\Interfaces\NotificationRepositoryInterface;
use App\Mail\NotificationMail;
use App\Models\Client;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository
    ) {
    }

    public function createNotification(array $data)
    {
        return $this->notificationRepository->create($data);
    }

    public function sendEmail($email, $title, $message)
    {
        Mail::to($email)->send(
            new NotificationMail($title, $message)
        );
    }

    public function sendLoginNotification(Client $client)
    {
        $this->createNotification([
            'client_id' => $client->id,
            'type' => 'login',
            'description' => 'Login successful.',
        ]);

        $this->sendEmail(
            $client->email,
            'New Login to Your Account',
            'A new login was detected on your account. If you do not recognize this activity, please change your password immediately.'
        );
    }

    public function sendBookingNotification(Client $client)
    {
        $this->createNotification([
            'client_id' => $client->id,
            'type' => 'booking_created',
            'description' => 'Your booking has been created successfully.',
        ]);

        $this->sendEmail(
            $client->email,
            'Your Booking Has Been Created',
            'Your booking has been created successfully. You can review the booking details from your account.'
        );
    }

    public function sendTripCreatedNotification(Client $client)
    {
        $this->createNotification([
            'client_id' => $client->id,
            'type' => 'trip_created',
            'description' => 'Your trip has been created successfully.',
        ]);

        $this->sendEmail(
            $client->email,
            'Your Trip Plan Is Ready',
            'Your new trip has been created successfully. You can now review and manage your trip plan from your account.'
        );
    }

    public function sendTripCancelledNotification(Client $client)
    {
        $this->createNotification([
            'client_id' => $client->id,
            'type' => 'trip_cancelled',
            'description' => 'Your trip has been cancelled.',
        ]);

        $this->sendEmail(
            $client->email,
            'Your Trip Has Been Cancelled',
            'Your trip has been cancelled successfully. Any applicable refund will be processed according to the cancellation policy.'
        );
    }

    public function sendPaymentSuccessNotification(Client $client)
    {
        $this->createNotification([
            'client_id' => $client->id,
            'type' => 'payment_success',
            'description' => 'Your payment has been completed successfully.',
        ]);

        $this->sendEmail(
            $client->email,
            'Payment Successful',
            'Your payment has been completed successfully. Your booking is now confirmed.'
        );
    }

    public function sendPaymentFailedNotification(Client $client)
    {
        $this->createNotification([
            'client_id' => $client->id,
            'type' => 'payment_failed',
            'description' => 'Your payment could not be completed.',
        ]);

        $this->sendEmail(
            $client->email,
            'Payment Failed',
            'Your payment could not be completed. Please try again or use a different payment method.'
        );
    }

    public function getClientNotifications(int $clientId)
    {
        return $this->notificationRepository->getByClient($clientId);
    }

    public function getUnreadNotifications(int $clientId)
    {
        return $this->notificationRepository->getUnreadByClient($clientId);
    }

    public function getUnreadCount(int $clientId)
    {
        return $this->notificationRepository->getUnreadCount($clientId);
    }

    public function markAsRead(int $notificationId)
    {
        return $this->notificationRepository->markAsRead($notificationId);
    }

    public function markAllAsRead(int $clientId)
    {
        return $this->notificationRepository->markAllAsRead($clientId);
    }
}
