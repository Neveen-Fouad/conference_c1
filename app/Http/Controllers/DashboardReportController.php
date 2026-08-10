<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardReportRequest;
use App\Repositories\Contracts\DashboardReportRepositoryInterface;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DashboardReportController extends Controller
{
    public function __construct(
        private readonly DashboardReportRepositoryInterface  $dashboardReportRepository
    ) {}

    /**
     * GET /api/admin/dashboard/statistics?month=&year=
     */
    public function statistics(DashboardReportRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $stats = $this->dashboardReportRepository->getStatistics(
            $validated['month'] ?? null,
            $validated['year'] ?? null,
        );

        return response()->json($stats);
    }

    public function exportPdf(DashboardReportRequest $request): Response
    {
        $validated = $request->validated();

        return $this->dashboardReportRepository->generatePdf(
            $validated['month'] ?? null,
            $validated['year'] ?? null,
        );
    }
}