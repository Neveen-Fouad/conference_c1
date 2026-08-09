<?php

namespace App\Providers;

use App\Interfaces\FavouriteRepositoryInterface;
use App\Interfaces\InterestRepositoryInterface;
use App\Interfaces\ReviewRepositoryInterface;
use App\Repositories\Eloquent\FavouriteRepository;
use App\Repositories\Eloquent\ReviewRepository;

use App\Repositories\Eloquent\InterestRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Eloquent\AuthRepository;


class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AuthRepositoryInterface::class,
            AuthRepository::class
        );
        $this->app->bind(
            FavouriteRepositoryInterface::class,
            FavouriteRepository::class
        );
        $this->app->bind(
            ReviewRepositoryInterface::class,
            ReviewRepository::class,
        );
        $this->app->bind(
          InterestRepositoryInterface::class,
          InterestRepository::class
        );

    }

    public function boot(): void
    {
        //
    }
}
