<?php

namespace App\Repositories;

use App\Models\Bookings;
use App\Interfaces\Repositories\Contracts\RevenueRepositoryInterface;

class RevenueRepository implements RevenueRepositoryInterface
{
    public function getTotalRevenue()
    {
        $bookings = Bookings::where('status', 'confirmed')->get();

        $totalRevenue = 0;

        foreach ($bookings as $booking) {
            $commission = $booking->total_price * 0.03;
            $totalRevenue += $commission;
        }

        return $totalRevenue;
    }
}
