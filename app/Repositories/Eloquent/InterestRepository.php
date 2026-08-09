<?php 
namespace App\Repositories\Eloquent;
use App\Interfaces\InterestRepositoryInterface;
use App\Models\interests;
use App\Repositories\BaseRepository;
class InterestRepository extends BaseRepository implements InterestRepositoryInterface
{
    public function __construct(interests $interests)
    {
        parent::__construct($interests);
    }
}
