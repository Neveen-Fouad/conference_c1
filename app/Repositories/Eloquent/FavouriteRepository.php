<?php

namespace App\Repositories\Eloquent;
use App\Interfaces\FavouriteRepositoryInterface;
use App\Models\favourites;
use App\Repositories\BaseRepository;


class FavouriteRepository extends BaseRepository implements FavouriteRepositoryInterface
{
    public function __construct(favourites $model)
    {
        parent::__construct($model);
    }
    protected function currentClientId()
    {
        return auth('api')->user()?->client?->id;
    }
    public function getAll()
    {
    
        return $this->model
        ->where('client_id',$this->currentClientId())
        ->paginate(10);
    
    }

    public function create(array $data)
    {
        $data['client_id'] = $this->currentClientId();
        return $this->model->create($data);
    }

    public function delete($id)
    {
        $favourite=$this->model
            ->where('client_id',$this->currentClientId())
            ->where('id',$id)
            ->firstOrFail();
            return $favourite->delete();
          
    }
    public function filterFavouriteByType(string $type)
    {
        return $this->model
        ->where('client_id',$this->currentClientId())
        ->where('type',$type)
        ->paginate(10);
   

    }
}