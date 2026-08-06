<?php

namespace App\Providers;


use App\Repositories\Contracts\TripRepositoryInterface;
use App\Repositories\Eloquent\TripRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Eloquent\BookingRepository;
use App\Repositories\Contracts\FlightsRepositoryInterface;
use App\Repositories\Eloquent\FlightsRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AuthRepositoryInterface::class,
            AuthRepository::class, 

        );

        $this->app->bind(
            TripRepositoryInterface::class,
            TripRepository::class,
        );

        $this->app->bind(
            BookingRepositoryInterface::class,
            BookingRepository::class

        );
        // $this->app->bind(
        //     FlightsRepositoryInterface::class,
        //     FlightsRepository::class
            
        // );
    }

    public function boot(): void
    {
        //
    }
}
