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
    // public function trips(){
    //     return $this->hasMany(favourites::class);
    // }
    public function favourites(){
        return $this->hasMany(favourites::class);
    }
    // public function client_has_ineterests(){
    //     return $this->hasMany(client_has_ineterests::class);
    // }
    public function trips()
{
    return $this->belongsToMany(
        trip::class,
        'client_has_trips',
        'client_id',
        'trips_id'
    );}
    public function interests()
{
    return $this->belongsToMany(
        Interests::class,
        'client_has_interests', // pivot table
        'client_id',            // foreign key on pivot for Client
        'interests_id'          // foreign key on pivot for Interests
    );}

    public function getEmailAttribute()
    {
        return $this->user?->email;
    }

}
