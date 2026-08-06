<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interests extends Model
{
    protected $fillable = [
        'name',
    ];

    public function clients()
    {
        return $this->belongsToMany(
            Client::class,
            'client_has_interests',
            'interests_id',
            'client_id'
        );
    }
}