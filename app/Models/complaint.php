<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class complaint extends Model
{
    //
     protected $fillable = [
       'email',
       'title',
       'phone',
       'description',
       
    ];
}
