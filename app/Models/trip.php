<?php

namespace App\Models;


use App\Enum\FavouriteType;
use App\Enum\ReviewType;
use Illuminate\Database\Eloquent\Model;

    class trip extends Model
{
    //
     protected $fillable = [

    'client_id',
    'classes',
    'destination',
    'number_of_travels',
    'estimated_expenses',
    'budget',
    'number_of_days',
    'start_date',
    'is_fav',
    'style',
];

    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function details()

{
    return $this->hasMany(details::class);
}

    public function favourites()
    {
        return $this->hasMany(favourites::class, 'favouriteable_id')
        ->where('type',FavouriteType::Trip->value);
    }
    public function reviews()
    {
        return $this->hasMany(review::class,'reviewable_id')
        ->where('type' , ReviewType::Trip->value);
    }
   
}
