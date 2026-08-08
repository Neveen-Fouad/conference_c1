<?php
namespace App\Enum;
enum ReviewStatus:string{
    case pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
