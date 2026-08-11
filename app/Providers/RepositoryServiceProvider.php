<?php

namespace App\Providers;

use App\Interfaces\FavouriteRepositoryInterface;
use App\Interfaces\InterestRepositoryInterface;
use App\Interfaces\ReviewRepositoryInterface;
use App\Interfaces\TripRepositoryInterface;
use App\Repositories\Eloquent\FavouriteRepository;
use App\Repositories\Eloquent\ReviewRepository;
use App\Repositories\Eloquent\TripRepository;

use App\Repositories\Eloquent\InterestRepository;

use App\Repositories\SettingRepository;
use Illuminate\Support\ServiceProvider;

use App\Interfaces\NotificationRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Interfaces\RevenueRepositoryInterface;

use App\Repositories\Contracts\AuthRepositoryInterface;

use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Contracts\ProfileRepositoryInterface;
use App\Repositories\Eloquent\ProfileRepository;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Eloquent\DashboardRepository;

use App\Repositories\NotificationRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\RevenueRepository;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Eloquent\BookingRepository;


use App\Repositories\Contracts\FlightsRepositoryInterface;
use App\Repositories\Eloquent\FlightsRepository;
use App\Repositories\Contracts\ChatConversationRepositoryInterface;
use App\Repositories\Contracts\ChatMessageRepositoryInterface;
use App\Repositories\Eloquent\ChatConversationRepository;
use App\Repositories\Eloquent\ChatMessageRepository;use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\ComplaintRepositoryInterface;
use App\Interfaces\SettingRepositoryInterface;
use App\Repositories\Contracts\DashboardReportRepositoryInterface;
use App\Repositories\Eloquent\DashboardReportRepository;
use App\Repositories\UserRepository;
use App\Repositories\ComplaintRepository;
use App\Services\DashboardReportService;

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

        $this->app->bind(
            ProfileRepositoryInterface::class,
            ProfileRepository::class
        );

        $this->app->bind(
            DashboardRepositoryInterface::class,
            DashboardRepository::class
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

        // $this->app->bind(
        //     FlightsRepositoryInterface::class,
        //     FlightsRepository::class

        // );
        $this->app->bind(
            ChatConversationRepositoryInterface::class,
            ChatConversationRepository::class
        );

        $this->app->bind(
            ChatMessageRepositoryInterface::class,
            ChatMessageRepository::class
        );

        $this->app->bind(
            AuthRepositoryInterface::class,
            AuthRepository::class
        );

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

        $this->app->bind(
           DashboardReportRepositoryInterface::class,
           DashboardReportRepository::class
);
    }
    

    








    public function boot(): void
    {
        //
    }
}
