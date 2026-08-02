<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class review extends Model
{
    protected $fillable=[
        "image",
        "rating",
        "description",
        ];
    public function client()
    {
        return $this->belongsTo(client::class);
    }
}
