<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class bookings extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'type',
        'provider',
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
    ];

    
    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'booking_date' => 'date',
        'details' => 'array',
        'total_price' => 'decimal:2',
    ];

    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }


    public function scopeHotels(Builder $query): Builder
    {
        return $query->where('type', 'hotel');
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
