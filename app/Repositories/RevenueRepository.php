<?php

namespace App\Repositories;

use App\Interfaces\RevenueRepositoryInterface;
use App\Models\Booking;

class RevenueRepository implements RevenueRepositoryInterface
{
    public function getTotalRevenue()
    {
        $bookings = Booking::where('status', 'confirmed')->get();
        $totalRevenue = 0;
        foreach ($bookings as $booking) {
            $commission = $booking->total_price * 0.03;
            $totalRevenue += $commission;
        }

        return $totalRevenue;
    }
}
