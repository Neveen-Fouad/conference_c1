<?php

namespace App\Providers;

use App\Interfaces\FavouriteRepositoryInterface;
use App\Interfaces\ReviewRepositoryInterface;
use App\Repositories\Eloquent\FavouriteRepository;
use App\Repositories\Eloquent\ReviewRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Eloquent\BookingRepository;


class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AuthRepositoryInterface::class,
            AuthRepository::class
        );

        $this->app->bind(
            BookingRepositoryInterface::class,
            BookingRepository::class
        );
        $this->app->bind(
            FavouriteRepositoryInterface::class,
            FavouriteRepository::class
        );
        $this->app->bind(
            ReviewRepositoryInterface::class,
            ReviewRepository::class,
        );

    }

    public function boot(): void
    {
        //
    }
}
