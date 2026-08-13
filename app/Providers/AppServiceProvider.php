<?php

namespace App\Providers;

use App\Models\Trip;
use App\Models\User;
use App\Policies\TripPolicy;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        Gate::policy(Trip::class, TripPolicy::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::createUrlUsing(function (User $user): string {
            $signedUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );

            $query = [];
            parse_str((string) parse_url($signedUrl, PHP_URL_QUERY), $query);

            return rtrim((string) config('app.frontend_url'), '/')
                .'/pages/verify-email.html?'
                .http_build_query([
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                    'expires' => $query['expires'] ?? null,
                    'signature' => $query['signature'] ?? null,
                ]);
        });
    }
}
