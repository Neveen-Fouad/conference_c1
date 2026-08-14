<?php

namespace App\Models;

use App\Enum\FavouriteType;
use App\Enum\ReviewType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    protected $fillable = [
        'estimated_expenses',
        'style',
        'number_of_travels',
        'classes',
        'destination',
        'start_date',
        'budget',
        'number_of_days',
        'is_ai_generated',
    ];

    protected $casts = [
        'start_date' => 'date',
        'is_ai_generated' => 'boolean',
    ];

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(
            Client::class,
            'client_has_trips',
            'trips_id',
            'client_id'
        );
    }

    public function details(): HasMany
    {
        return $this->hasMany(TripDetail::class);
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class, 'favouriteable_id')
            ->where('type', FavouriteType::Trip->value);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewable_id')
            ->where('type', ReviewType::Trip->value);
    }

    /** @return HasMany<TripMemory, $this> */
    public function memories(): HasMany
    {
        return $this->hasMany(TripMemory::class);
    }

    /**
     * Normalized details used for favourites (and similar card contexts).
     */
    public function toFavouriteDetails(): array
    {
        return [
            'name' => $this->destination ?? 'Trip',
            'description' => $this->style ?? null,
            'image' => null,
            'location' => $this->destination ?? null,
            'rating' => null,
            'start_date' => $this->start_date?->toDateString(),
            'number_of_days' => $this->number_of_days,
            'budget' => $this->budget,
        ];
    }
}
