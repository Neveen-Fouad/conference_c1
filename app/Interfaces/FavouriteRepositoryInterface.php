<?php
namespace App\Interfaces;
use App\Interfaces\BaseRepositoryInterface;

interface FavouriteRepositoryInterface extends BaseRepositoryInterface
{
   
    public function filterFavouriteByType(string $type);

}