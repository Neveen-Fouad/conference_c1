<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripMemory extends Model
{
    protected $fillable = ['trip_id', 'client_id', 'type', 'content', 'caption'];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function getContentUrlAttribute()
{
    return $this->content;
}
}