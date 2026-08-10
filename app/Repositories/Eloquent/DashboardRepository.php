<?php
namespace App\Repositories\Eloquent;

use App\Models\Client;
use App\Models\trip;
use App\Models\bookings;
use App\Models\favourites;
use Illuminate\Support\Facades\Cache;
use App\Repositories\Contracts\DashboardRepositoryInterface;

class DashboardRepository implements DashboardRepositoryInterface{
    public function getSavedTrips(int $userId){
        $client = Client::where('user_id', $userId)->first();
        if (!$client){
            return null;
        }
        
        return $client->trips()
            ->latest()
            ->paginate(10);
    }

    public function getFavouriteDestinations(int $userId){
        return Cache::remember("user_{$userId}_favourite_destinations", now()->addMinutes(30), function () use ($userId){
            $client = Client::where('user_id', $userId)->first();

            if (!$client){
                return null;
            }
            return favourites::where('client_id', $client->id)
            ->latest()
            ->paginate(10);
        });
    }

    public function getBookingHistory(int $userId){
        return Cache::remember("user_{$userId}_booking_history", now()->addMinutes(30), function () use ($userId){
            $client = Client::where('user_id', $userId)->first();

            if (!$client){
                return null;
            }

            return bookings::where('client_id', $client->id)
            ->latest()
            ->paginate(10);
        });

    }

    public function getProfileSettings(int $userId){
        return Cache::remember("user_{$userId}_profile_settings", now()->addMinutes(30), function () use ($userId){
            $client = Client::with('user')
            ->where('user_id', $userId)
            ->first();

            if (!$client){
                return null;
            }

            return[
                'first_name' => $client->user->first_name,
                'last_name'  => $client->user->last_name,
                'email'      => $client->user->email,
                'phone'      => $client->phone,
                'birth_date' => $client->birth_date,
                'latitude'   => $client->latittude,
                'longitude'  => $client->long,
            ];
        });

    }

    public function updateProfileSettings(int $userId, array $data){
        $client = Client::with('user')
            ->where('user_id', $userId)
            ->first();

            if (!$client){
                return null;
            }

            $client->user->update([
                'first_name' => $data['first_name'] ?? $client->user->first_name,
                'last_name'  => $data['last_name'] ?? $client->user->last_name,
            ]);

            $client->update([
                'phone'      => $data['phone'] ?? $client->phone,
                'birth_date' => $data['birth_date'] ?? $client->birth_date,
                'latittude'  => $data['latittude'] ?? $client->latittude,
                'long'       => $data['long'] ?? $client->long,
            ]);

            Cache::forget("user_{$userId}_profile_settings");

            return $client->fresh()->load('user');
    }

    public function getStatistics(int $userId){
        $client = Client::where('user_id', $userId)->first();
        if (!$client){
            return null;
        }
        
        return [
            'total_trips' => $client->trips()->count(),
            'favorite_trips' => $client->trips()
                ->where('is_fav', true)
                ->count(),

            'total_bookings' => bookings::where('client_id', $client->id)->count(),
            'total_favourites' => favourites::where('client_id', $client->id)->count(),
            'total_budget' => $client->trips()->sum('budget'),
            'total_estimated_expenses' => $client->trips()
                ->sum('estimated_expenses'),
        ];
    }
}