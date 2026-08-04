<?php

namespace App\Providers;


use App\Repositories\Contracts\TripRepositoryInterface;
use App\Repositories\Eloquent\TripRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Eloquent\AuthRepository;

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
    }

    public function boot(): void
    {
        //
    }
}
