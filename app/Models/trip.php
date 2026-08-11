<?php

namespace App\Models;

use App\Enum\FavouriteType;
use App\Enum\ReviewType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class trip extends Model
{
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

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(
            client::class,
            'client_has_trips',
            'trips_id',
            'client_id'
        );
    }

    public function details(): HasMany
    {
        return $this->hasMany(details::class);
    }

    public function favourites()
    {
        return $this->hasMany(favourites::class, 'favouriteable_id')
            ->where('type', FavouriteType::Trip->value);
    }

    public function reviews()
    {
        return $this->hasMany(review::class, 'reviewable_id')
            ->where('type', ReviewType::Trip->value);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(client::class, 'trip_participants');
    }

    public function memories(): HasMany
    {
        return $this->hasMany(TripMemory::class);
    }
}