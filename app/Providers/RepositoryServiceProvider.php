<?php

namespace App\Providers;

use App\Interfaces\NotificationRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Contracts\TripRepositoryInterface;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Eloquent\TripRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\BaseRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Repositories\RevenueRepository;
use App\Interfaces\Repositories\Contracts\RevenueRepositoryInterface;


class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AuthRepositoryInterface::class,
            AuthRepository::class);

        $this->app->bind(
            TripRepositoryInterface::class,
            TripRepository::class);

        $this->app->bind(
            PaymentRepositoryInterface::class,
            PaymentRepository::class);

        $this->app->bind(
            NotificationRepositoryInterface::class,
            NotificationRepository::class
        );

        $this->app->bind(
            RevenueRepositoryInterface::class,
            RevenueRepository::class
        );

    }

    public function boot(): void
    {
        //
    }
}
