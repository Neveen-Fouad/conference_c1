<?php
namespace App\Interfaces;
use App\Interfaces\BaseRepositoryInterface;
interface FavouriteRepositoryInterface extends BaseRepositoryInterface
{
    public function addFavourite(array $data , $client_id);

    public function getMyFavourites(int $cilent_id);

    public function filterFavouritesByType(int $cilent_id,string $type);

    public function removeFavourite(int $cilent_id, string $type ,string $parameter);

    public function addFavouriteToTrip(int $trip_id, int $favourite_id ,int $client_id);

}