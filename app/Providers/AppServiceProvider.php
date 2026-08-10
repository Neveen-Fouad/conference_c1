<?php

namespace App\Providers;


use App\Models\trip;
use App\Policies\TripPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\ComplaintRepositoryInterface;
use App\Interfaces\SettingRepositoryInterface;
use App\Interfaces\DashboardReportServiceInterface;
use App\Services\DashboardReportService;


use App\Repositories\UserRepository;
use App\Repositories\ComplaintRepository;
use App\Repositories\SettingRepository;
use App\Interfaces\TripRepositoryInterface;
use App\Repositories\TripRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

       

        Gate::policy(trip::class, TripPolicy::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
