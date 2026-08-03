<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class favourites extends Model
{
    //
    protected $fillable = [
       'favouriteable_type',
       'favouriteable_id',
       'client_id'
        ];
    public function client(){
        return $this->belongsTo(client::class);
    }
    public function favouriteable()
    {
        return $this->morphTo();

    }
       
}
