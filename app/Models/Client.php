<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'birth_date',
        'location',
        'interest', // legacy column — consider removing later if unused
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(bookings::class);
    }

    public function interests()
    {
        return $this->belongsToMany(
            Interests::class,
            'client_has_interests',
            'client_id',
            'interests_id'
        );
    }
}