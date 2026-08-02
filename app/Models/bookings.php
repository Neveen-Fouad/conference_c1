<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bookings extends Model
{
    //

     protected $fillable = [
       'type',
       'number_of_days',
       'number_of_booking',
       'classes',
       'status',
       'client_id'
    ];
   

    function bookings(){
        return $this->belongsTo($client::class);
    }
}
