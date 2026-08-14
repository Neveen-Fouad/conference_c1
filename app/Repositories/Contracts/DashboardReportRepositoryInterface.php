<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Response;

interface DashboardReportRepositoryInterface
{
    public function getStatistics(?int $month = null, ?int $year = null): array;

    public function generatePdf(?int $month = null, ?int $year = null): Response;
}
