<?php

namespace App\Providers;

use App\Models\Trip;
use App\Policies\TripPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
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
        VerifyEmail::createUrlUsing(function ($notifiable): string {
            $id = $notifiable->getKey();
            $hash = sha1($notifiable->getEmailForVerification());

            $backendUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $id, 'hash' => $hash],
            );

            parse_str((string) parse_url($backendUrl, PHP_URL_QUERY), $query);

            return rtrim((string) config('app.frontend_url'), '/')
                .'/pages/verify-email?'
                .http_build_query([
                    'id' => $id,
                    'hash' => $hash,
                    'expires' => $query['expires'],
                    'signature' => $query['signature'],
                ]);
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify Your Email Address - '.config('app.name'))
                ->view('emails.verify-email', [
                    'url' => $url,
                    'user' => $notifiable,
                ]);
        });


        ResetPassword::createUrlUsing(function (object $notifiable, string $token): string {
    return rtrim((string) config('app.frontend_url'), '/')
        .'/pages/reset-password?'
        .http_build_query([
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
});
    }
}
