<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
       'phone',
       'birth_date',
       'long',
       'latittude',
       'user_id',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }

     public function bookings(){
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
    // public function client_has_ineterests(){
    //     return $this->hasMany(client_has_ineterests::class);
    // }
    public function interests()
{
    return $this->belongsToMany(
        Interests::class,
        'client_has_interests', // pivot table
        'client_id',            // foreign key on pivot for Client
        'interests_id'          // foreign key on pivot for Interests
    );
}}