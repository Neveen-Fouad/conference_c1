<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $appends = ['logo_url'];

    protected $fillable = [
        'name',
        'phone',
        'email',
        'slogan',
        'logo',
        'facebook',
        'instagram',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? url('/storage/' . ltrim($this->logo, '/')) : null;
    }
}
