<?php

namespace App\Providers;

use App\Interfaces\NotificationRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Interfaces\FavouriteRepositoryInterface;
use App\Interfaces\ReviewRepositoryInterface;
use App\Repositories\Eloquent\FavouriteRepository;
use App\Repositories\Eloquent\ReviewRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Contracts\TripRepositoryInterface;
use App\Repositories\Contracts\ProfileRepositoryInterface;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Eloquent\ProfileRepository;
use App\Repositories\Eloquent\DashboardRepository;
use App\Repositories\Eloquent\TripRepository;
use App\Repositories\Eloquent\BookingRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\PaymentRepository;

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
            ReviewRepository::class
        );
        $this->app->bind(
            NotificationRepositoryInterface::class,
            NotificationRepository::class
        );
    }
    public function boot(): void
    {
        //
    }
}
