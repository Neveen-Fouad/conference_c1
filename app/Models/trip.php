<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class trip extends Model
{
    protected $fillable = [
        'name',
        'phone',
        "estimated_price",
        "style",
        "number_of_days",
        "number_of_travellers",
        "classes",
        "destination",
        "start_date",
        "end_date",
        "budget",


    ];
    public function client()
    {
        return $this->belongsTo(client::class);
    }
    public function details()

{
    return $this->hasMany(details::class);
}
}
