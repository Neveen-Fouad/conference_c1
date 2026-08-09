<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class details extends Model
{
    //
     protected $fillable = [
       'day',
       'trip_id',
       'expenses',
       'plan',
       'title',
    
    ];
    public function trip(){
        return $this->belongsTo(trip::class);
    }
}
