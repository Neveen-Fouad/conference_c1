<?php

namespace App\Repositories\Eloquent;

use App\Enum\FavouriteType;
use App\Models\Booking;
use App\Models\Favourite;
use App\Models\Trip;
use App\Models\User;
use App\Repositories\Contracts\DashboardReportRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DashboardReportRepository implements DashboardReportRepositoryInterface
{
    private const CACHE_TTL_SECONDS = 600; // 10 minutes

    public function getStatistics(?int $month = null, ?int $year = null): array
    {
        $month ??= now()->month;
        $year ??= now()->year;

        $cacheKey = "dashboard_report_stats_{$year}_{$month}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($month, $year) {
            return $this->buildStatistics($month, $year);
        });
    }

    public function generatePdf(?int $month = null, ?int $year = null): Response
    {
        $stats = $this->getStatistics($month, $year);

        $chartCacheKey = "dashboard_report_charts_{$stats['period_year']}_{$stats['period_month']}";

        $charts = Cache::remember($chartCacheKey, self::CACHE_TTL_SECONDS, function () use ($stats) {
            return [
                'revenue_trend' => $this->fetchChartAsBase64($this->revenueLineConfig($stats['trip_stats']['revenue_last_6_months'])),
                'destinations' => $this->fetchChartAsBase64($this->destinationsBarConfig($stats['trip_stats']['most_popular_destinations'])),
                'verification' => $this->fetchChartAsBase64($this->verificationDoughnutConfig($stats['user_stats'])),
            ];
        });

        $pdf = Pdf::loadView('dashboard-pdf', [
            'stats' => $stats,
            'charts' => $charts,
        ])->setPaper('a4');

        return $pdf->download('dashboard-report-'.now()->format('Y-m-d').'.pdf');
    }

    private function buildStatistics(int $month, int $year): array
    {
        $periodDate = now()->setMonth($month)->setYear($year);

        return [
            'generated_at' => now()->toDateTimeString(),
            'period_label' => $periodDate->format('F Y'),
            'period_month' => $month,
            'period_year' => $year,

            'trip_stats' => [
                'total_trips' => Trip::count(),

                'monthly_trips' => Trip::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->count(),

                'favorite_trips' => Favourite::where('type', FavouriteType::Trip->value)->count(),

                'average_budget' => round(Trip::avg('budget'), 2),

                // No "paid" status exists yet in bookings — every row is
                // "pending" right now, so we sum everything. Tighten this
                // to ->where('status', 'confirmed') (or similar) once your
                // app starts writing other status values.
                'total_revenue' => (float) Booking::sum('total_price'),

                'monthly_revenue' => (float) Booking::whereMonth('booking_date', $month)
                    ->whereYear('booking_date', $year)
                    ->sum('total_price'),

                'most_popular_destinations' => Trip::select('destination')
                    ->selectRaw('COUNT(*) as count')
                    ->groupBy('destination')
                    ->orderByDesc('count')
                    ->limit(5)
                    ->get()
                    ->map(fn ($row) => [
                        'destination' => $row->destination,
                        'count' => (int) $row->getAttribute('count'),
                    ])
                    ->toArray(),

                'revenue_last_6_months' => collect(range(5, 0))
                    ->map(function (int $monthsAgo) use ($periodDate) {
                        $date = $periodDate->copy()->subMonths($monthsAgo);

                        return [
                            'month' => $date->format('M'),
                            'revenue' => (float) Booking::whereMonth('booking_date', $date->month)
                                ->whereYear('booking_date', $date->year)
                                ->sum('total_price'),
                        ];
                    })
                    ->values()
                    ->toArray(),
            ],

            'user_stats' => [
                'total_users' => User::count(),

                'monthly_users' => User::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->count(),

                'verified_users' => User::whereNotNull('email_verified_at')->count(),
                'unverified_users' => User::whereNull('email_verified_at')->count(),
            ],
        ];
    }

    private function revenueLineConfig(array $series): array
    {
        return [
            'type' => 'line',
            'data' => [
                'labels' => array_column($series, 'month'),
                'datasets' => [[
                    'label' => 'Revenue',
                    'data' => array_column($series, 'revenue'),
                    'borderColor' => '#2F6FED',
                    'backgroundColor' => 'rgba(47,111,237,0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ]],
            ],
            'options' => [
                'plugins' => ['legend' => ['display' => false]],
            ],
        ];
    }

    private function destinationsBarConfig(array $destinations): array
    {
        return [
            'type' => 'bar',
            'data' => [
                'labels' => array_column($destinations, 'destination'),
                'datasets' => [[
                    'label' => 'Trips',
                    'data' => array_column($destinations, 'count'),
                    'backgroundColor' => '#2F6FED',
                ]],
            ],
            'options' => [
                'indexAxis' => 'y',
                'plugins' => ['legend' => ['display' => false]],
            ],
        ];
    }

    private function verificationDoughnutConfig(array $userStats): array
    {
        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => ['Verified', 'Unverified'],
                'datasets' => [[
                    'data' => [$userStats['verified_users'], $userStats['unverified_users']],
                    'backgroundColor' => ['#16A34A', '#F59E0B'],
                ]],
            ],
        ];
    }

    /**
     * Fetches a QuickChart image server-side and returns it as a base64
     * data URI, so it can be embedded directly in the PDF without dompdf
     * needing to make its own remote request.
     *
     * withoutVerifying() skips SSL certificate verification — needed on
     * many local XAMPP/Windows setups with an outdated CA bundle. Remove
     * it once deployed to a proper server with valid SSL configuration.
     */
    private function fetchChartAsBase64(array $config): string
    {
        $url = 'https://quickchart.io/chart?width=700&height=300&backgroundColor=white&c='.urlencode(json_encode($config));

        try {
            $response = Http::withoutVerifying()->timeout(15)->get($url);

            if ($response->successful()) {
                return 'data:image/png;base64,'.base64_encode($response->body());
            }
        } catch (\Exception $e) {
            // Fall through to placeholder below if the request fails
            // (e.g. no internet access on this machine).
        }

        // 1x1 transparent PNG placeholder if the chart couldn't be fetched,
        // so the PDF still renders instead of showing a broken image icon.
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';
    }
}
