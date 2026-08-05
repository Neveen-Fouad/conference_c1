<?php


use App\Interfaces\FavouriteRepositoryInterface;
use App\Models\favourites;
use App\Repositories\BaseRepository;


class FavouriteRepository extends BaseRepository implements FavouriteRepositoryInterface
{
    public function __construct(favourites $model){
        parent::__construct($model);
    }
    public function index()
    {
    
        return $this->model
        ->where('client_id',auth('api')->id())->get();
    
    }

    public function store(array $data)
    {
        return $this->model->create($data);
    }

    public function destroy($type, $favouriteable_id)
    {

        $favourite=$this->model
            ->where('client_id',auth('api')->id())
            ->where('type',$type)
            ->where('favouriteable_id',$favouriteable_id)
            ->firstOrFail();
            $favourite->delete();
        
    }
    public function filter( $type)
    {
        $query=$this->model
        ->where('client_id',auth('api')->id())
        ->where('type',$type)
        ->get();
   

    }
}