<?php

namespace App\Models;

use App\Enum\FavouriteType;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    //
    protected $fillable = [
       'type',
       'favouriteable_id',
       'client_id'
    ];

    protected $casts = [
        'type' => FavouriteType::class,
    ];
    public function client(){
        return $this->belongsTo(Client::class);
    }
    public function trip()
    {
        return $this->belongsTo(Trip::class, 'favouriteable_id');

    }
       
}
