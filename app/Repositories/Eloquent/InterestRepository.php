<?php 
namespace App\Repositories;
use App\Interfaces\InterestRepositoryInterface;
use App\Models\interests;
class InterestRepository extends BaseRepository implements InterestRepositoryInterface
{
    public function __construct(interests $interests)
    {
        parent::__construct($interests);
    }
}
