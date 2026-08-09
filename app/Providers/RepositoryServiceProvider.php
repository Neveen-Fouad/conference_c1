<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\NotificationRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Interfaces\RevenueRepositoryInterface;

use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Contracts\TripRepositoryInterface;

use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Eloquent\TripRepository;

use App\Repositories\NotificationRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\RevenueRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {


        $this->app->bind(
            PaymentRepositoryInterface::class,
            PaymentRepository::class
        );

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
