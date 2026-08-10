<?php

namespace App\Providers;


use App\Models\trip;
use App\Policies\TripPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\ComplaintRepositoryInterface;
use App\Interfaces\SettingRepositoryInterface;

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
        
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            ComplaintRepositoryInterface::class,
            ComplaintRepository::class
        );

        $this->app->bind(
        SettingRepositoryInterface::class,
        SettingRepository::class
    );
        $this->app->bind(
    TripRepositoryInterface::class,
    TripRepository::class
);
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
