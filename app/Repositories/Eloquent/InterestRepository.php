<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\InterestRepositoryInterface;
use App\Models\Interest;
use App\Repositories\BaseRepository;

class InterestRepository extends BaseRepository implements InterestRepositoryInterface
{
    public function __construct(Interest $interests)
    {
        parent::__construct($interests);
    }
}
