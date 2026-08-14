<?php

namespace App\Models;

use App\Enum\ReviewStatus;
use App\Enum\ReviewType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    //
    protected $fillable = [
        'description',
        'client_id',
        'rating',
        'type',
        'image',
        'status',
        'reviewable_id',
        'booking_id',
    ];

    protected $casts = [
        'type' => ReviewType::class,
        'status' => ReviewStatus::class,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);

    }



    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class, 'reviewable_id')
            ->where('type', ReviewType::Trip->value);
    }
}
