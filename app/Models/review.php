<?php

namespace App\Models;

use App\Enum\ReviewStatus;
use App\Enum\ReviewType;
use Illuminate\Database\Eloquent\Model;

class review extends Model
{
    //
    protected $fillable=[
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
    public function client(){
        return $this->belongsTo(Client::class);

    }
    public function interest(){
        return $this->belongsTo(interests::class,'interests_id');
    }
    public function trip(){
        return $this->belongsTo(trip::class, 'reviewable_id')
        ->where('type',ReviewType::Trip->value);
    }
}
