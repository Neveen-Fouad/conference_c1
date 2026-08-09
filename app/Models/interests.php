<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class interests extends Model
{
    //
    protected $fillable = [
       'name',
       'client_id',
       
       
    
    ];
    function interests(){
     return $this->belongsTo(client::class);
    }
}
