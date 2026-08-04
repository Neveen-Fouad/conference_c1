<?php
namespace App\Interfaces;
use App\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
interface FavouriteRepositoryInterface extends BaseRepositoryInterface
{
    public function index();

    public function store(array $data);

    public function destroy(string $type , string $favouriteable_id);

    public function filter(string $type);

}