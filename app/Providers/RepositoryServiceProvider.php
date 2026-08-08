<?php

namespace App\Providers;

use App\Interfaces\NotificationRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Contracts\TripRepositoryInterface;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Contracts\ProfileRepositoryInterface;
use App\Repositories\Eloquent\ProfileRepository;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Eloquent\DashboardRepository;
use App\Repositories\Eloquent\TripRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\BaseRepositoryInterface;
use App\Repositories\BaseRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AuthRepositoryInterface::class,
            AuthRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
