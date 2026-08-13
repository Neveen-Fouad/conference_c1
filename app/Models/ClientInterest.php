<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientInterest extends Model
{
   protected $table = 'client_has_interests';

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
    return $this->belongsTo(Interest::class, 'interests_id');
}
}
