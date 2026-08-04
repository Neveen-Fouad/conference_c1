<?php


use App\Interfaces\FavouriteRepositoryInterface;
use App\Models\favourites;
use App\Repositories\BaseRepository;


class FavoriteRepository extends BaseRepository implements FavouriteRepositoryInterface
{
    public function __construct(favourites $model){
        parent::__construct($model);
    }
    public function index()
    {
    
        $query=$this->model
        ->where('client_id',auth('api')->id());
        
    
        $query
        ->latest()
        ->paginate(10);
        // external api
      
    }

    public function store(array $data)
    {
       
        
        // external api
       
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
    public function filter( $type){

    }
}