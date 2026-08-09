<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\ComplaintRepositoryInterface;
use App\Interfaces\SettingRepositoryInterface;

use App\Repositories\UserRepository;
use App\Repositories\ComplaintRepository;
use App\Repositories\SettingRepository;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
