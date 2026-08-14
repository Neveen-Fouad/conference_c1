<?php

namespace App\Enum;

enum FavouriteType: string
{
    case Trip = 'trip';
    case Hotel = 'hotel';
    case Restaurant = 'restaurant';
    case Flight = 'flight';
    case Country = 'country';

}
