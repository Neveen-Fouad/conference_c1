<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'type',
        'booking_type',
        'provider',
        'provider_name',
        'external_reference_id',
        'number_of_days',
        'check_in_date',
        'check_out_date',
        'booking_date',
        'number_of_bookings',
        'classes',
        'status',
        'total_price',
        'currency',
        'details',
        'commisssion_rate',
        'commisssion_amount',
        'booked_at',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'booking_date' => 'date',
        'details' => 'array',
        'total_price' => 'decimal:2',
    ];

    /** @return BelongsTo<Client, $this> */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function scopeHotels(Builder $query): Builder
    {
        return $query->where('type', 'hotel');
    }

    /** @return HasOne<Review, $this> */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function scopeFlights(Builder $query): Builder
    {
        return $query->where('type', 'flight');
    }

    public function scopeRestaurants(Builder $query): Builder
    {
        return $query->where('type', 'restaurant');
    }
}
