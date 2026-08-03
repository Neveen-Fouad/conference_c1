<?php
namespace App\Interfaces;
use App\Interfaces\BaseRepositoryInterface;
interface FavouriteRepositoryInterface extends BaseRepositoryInterface
{
    public function index($request);

    public function store($request,$favouriteable_type,$favouriteable_id);

    public function destroy($favouriteable_type,$favouriteable_id);

}