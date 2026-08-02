<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class setting extends Model
{
    //
    
    protected $fillable = [
        "name",
        "phone",
        "email",
        "slogan",
        "logo",
        "facebook_link",
        "instagram_link",
    ];
}
