<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'client_id',
        'booking_id',
        'payment_reference',
        'payment_type',
        'amount',
        'currency',
        'gateway',
        'status',
        'gateway_reference',
        'gateway_transaction_id',
        'payment_method',
        'failure_reason',
        'gateway_response',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(
            Client::class,
            'client_id'
        );

    }


    public function bookings()
    {
        return $this->belongsTo(
            Bookings::class,
            'booking_id'
        );
    }
}

