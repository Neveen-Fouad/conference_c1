<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'phone',
        'birth_date',
        'long',
        'latittude',
        'user_id',

    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<Booking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);

    }

    /** @return HasMany<Notification, $this> */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // public function trips(){
    //     return $this->hasMany(Favourite::class);
    // }
    /** @return HasMany<Favourite, $this> */
    public function favourites(): HasMany
    {
        return $this->hasMany(Favourite::class);
    }

    // public function client_has_ineterests(){
    //     return $this->hasMany(client_has_ineterests::class);
    // }
    /** @return BelongsToMany<Trip, $this> */
    public function trips(): BelongsToMany
    {
        return $this->belongsToMany(
            Trip::class,
            'client_has_trips',
            'client_id',
            'trips_id'
        );
    }

    /** @return BelongsToMany<Interest, $this> */
    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(
            Interest::class,
            'client_has_interests', // pivot table
            'client_id',            // foreign key on pivot for Client
            'interests_id'          // foreign key on pivot for Interests
        );
    }

    public function getEmailAttribute()
    {
        return $this->user?->email;
    }
}
