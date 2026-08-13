<?php

namespace App\Interfaces;

interface FavouriteRepositoryInterface extends BaseRepositoryInterface
{
    public function filterFavouriteByType(string $type);
}
