<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    protected $fillable = [
       'phone',
       'birth_date',
       'location',
       'booking_id',
       
    ];

    function clients(){
        return $this->hasMany($bookings::class);

    }
}
