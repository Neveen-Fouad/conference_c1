<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class client_has_interests extends Model
{
   protected $fillable = [
    'client_id',
    'interests_id',
];

public function client()
{
    return $this->belongsTo(Client::class);
}

public function interest()
{
    return $this->belongsTo(interests::class, 'interests_id');
}
}
