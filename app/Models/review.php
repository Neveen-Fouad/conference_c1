<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class review extends Model
{
    //
    protected $fillable=[
        'description',
        'client_id',
    ];
    public function client(){
        return $this->belongsTo(Client::class);

    }
    public function interest(){
        return $this->belongsTo(interests::class,'interests_id');
    }
}
