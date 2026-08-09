<?php

namespace App\Models;

use App\Enum\FavouriteType;
use Illuminate\Database\Eloquent\Model;

class favourites extends Model
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
        return $this->belongsTo(client::class);
    }
    public function trip()
    {
        return $this->belongsTo(trip::class, 'favouriteable_id');

    }
       
}
