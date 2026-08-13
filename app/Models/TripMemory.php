<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripMemory extends Model
{
    protected $fillable = ['trip_id', 'client_id', 'type', 'content', 'caption'];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getContentUrlAttribute()
    {
        return $this->content;
    }
}
