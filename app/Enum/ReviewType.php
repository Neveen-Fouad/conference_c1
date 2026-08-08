<?php
namespace App\Enum;
enum ReviewType:string
{
    case Trip = 'trip';
    case Hotel = 'hotel';
    case Restaurant = 'restaurant';
    case Flight = 'flight';

}