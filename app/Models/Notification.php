<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'client_id',
        'type',
        'description',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
