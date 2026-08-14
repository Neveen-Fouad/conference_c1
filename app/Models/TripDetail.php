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

    /**
     * Keep itinerary plans structured when they are read from or written to
     * the database. This makes the trip-days API return `plan` as an object
     * instead of an escaped JSON string.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'plan' => 'array',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
