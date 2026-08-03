<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class favourites extends Model
{
    //
    protected $fillable = [
       'type',
       'parameter',
       'payload',
       'client_id'
        ];
    public function client(){
        return $this->belongsTo($Client::class);
    }
       
}
