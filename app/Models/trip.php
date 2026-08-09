<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

    class trip extends Model
{
    //
     protected $fillable = [
        "estimated_expenses",
        "style",
        "number_of_travels",
        "classes",
        "destination",
        "start_date",
        "end_date",
        "budget",
        "is_ai_generated"


    ];
    public function clients()
{
    return $this->belongsToMany(
        client::class,
        'client_has_trips',
        'trips_id',  
        'client_id'   
    );
}
    public function details()

{
    return $this->hasMany(details::class);
}
}
