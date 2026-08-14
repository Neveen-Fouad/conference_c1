<?php

namespace App\Providers;

use App\Models\Trip;
use App\Models\User;
use App\Policies\TripPolicy;
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
        VerifyEmail::createUrlUsing(function ($notifiable) {
    $id = $notifiable->getKey();
    $hash = sha1($notifiable->getEmailForVerification());

    $backendUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $id, 'hash' => $hash]
    );

    $parsedUrl = parse_url($backendUrl);
    $query = [];
    if (isset($parsedUrl['query'])) {
        parse_str($parsedUrl['query'], $query);
    }

    $frontendUrl = rtrim(config('app.frontend_url'), '/') . '/pages/verify-email.html';

    return $frontendUrl . '?' . http_build_query([
        'id' => $id,
        'hash' => $hash,
        'expires' => $query['expires'] ?? '',
        'signature' => $query['signature'] ?? '',
    ]);
});

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify Your Email Address - ' . config('app.name'))
                ->view('emails.verify-email', [
                    'url' => $url,
                    'user' => $notifiable,
                ]);
        });
    }
}
