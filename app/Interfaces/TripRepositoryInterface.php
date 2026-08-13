<?php
namespace App\Interfaces;

interface TripRepositoryInterface extends BaseRepositoryInterface{
    public function statistics();
    public function findByUserId(int $userId);
    public function getTripDetails(int $tripId);

    public function getPreMadeTrips();
    public function bookTrip(int $tripId, int $clientId);
}