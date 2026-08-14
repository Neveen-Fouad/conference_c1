<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripDetail extends Model
{
    protected $table = 'details';

    //
    protected $fillable = [
        'day',
        'trip_id',
        'expenses',
        'plan',
        'title',

    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
