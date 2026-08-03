<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class notifications extends Model
{
    //
     protected $fillable = [
       'type',
       'description',
       'client_id',


    ];
    function notifications(){
        return $this->belongsTo(client::class);

    }
}
