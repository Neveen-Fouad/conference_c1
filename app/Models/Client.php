<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
       'phone',
       'birth_date',
       'location',
       'booking_id',

    ];
    public function user(){
        return $this->belongsTo(User::class);
    }

     public function Bookings(){
        return $this->hasMany(bookings::class);

    }
    public function notifications(){
        return $this->hasMany(bookings::class);
    }
    public function reviews(){
        return $this->hasMany(review::class);
    }
    public function trips(){
        return $this->hasMany(favourites::class);
    }
    public function favourites(){
        return $this->hasMany(favourites::class);
    }
    public function client_has_ineterests(){
        return $this->hasMany(client_has_interests::class);
        return $this->hasMany(client_has_interests::class);
    }
    public function getEmailAttribute()
    {
        return $this->user?->email;
    }
}
