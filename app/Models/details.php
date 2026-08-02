<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class details extends Model
{
    //
     protected $fillable = [
       'day',
       'trip_id',
       'day_expenses',
       'day_plan',
       'title',
    
    ];
    function trip_details(){
        return $this->belongsTo($trip::class);
    }
}
