<?php

namespace App\Repositories\Contracts;

interface DashboardRepositoryInterface
{
    public function getSavedTrips(int $userId);

    public function getFavouriteDestinations(int $userId);

    public function getBookingHistory(int $userId);

    public function getProfileSettings(int $userId);

    public function updateProfileSettings(int $userId, array $data);

    public function getStatistics(int $userId);
}
