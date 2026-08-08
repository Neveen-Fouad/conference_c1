<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RevenueService;

class RevenueController extends Controller
{
    protected RevenueService $revenueService;

    public function __construct(RevenueService $revenueService)
    {
        $this->revenueService = $revenueService;
    }
    public function totalRevenue()
    {
        $revenue = $this->revenueService->getTotalRevenue();
        return response()->json([
            'total_revenue' => $revenue
        ]);
    }
}
