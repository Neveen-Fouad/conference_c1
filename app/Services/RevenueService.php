<?php

namespace App\Services;

use App\Interfaces\RevenueRepositoryInterface;

class RevenueService
{
    protected RevenueRepositoryInterface $revenueRepository;

    public function __construct(
        RevenueRepositoryInterface $revenueRepository
    ) {
        $this->revenueRepository = $revenueRepository;
    }

    public function getTotalRevenue()
    {
        return $this->revenueRepository->getTotalRevenue();
    }
}
