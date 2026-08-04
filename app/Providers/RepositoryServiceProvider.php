<?php

namespace App\Providers;


use App\Interfaces\BaseRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\TripRepositoryInterface;
use App\Repositories\Eloquent\TripRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\PaymentRepository;
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
            PaymentRepositoryInterface::class,
            PaymentRepository::class
        );






    }

    public function boot(): void
    {
        //
    }
}
