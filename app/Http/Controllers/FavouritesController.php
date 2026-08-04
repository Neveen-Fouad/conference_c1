<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFavouriteRequest;
use App\Interfaces\FavouriteRepositoryInterface;
use Illuminate\Http\Request;



class FavouritesController extends Controller
{
    protected $FavouriteRepository;
    public function __construct(FavouriteRepositoryInterface $FavouriteRepository){
        $this->FavouriteRepository=$FavouriteRepository;
    }

    
    public function index(Request $request){
         $this->FavouriteRepository->index($request);
         return response()->json(['message'=>'Favourites retrieved successfully'],200);
    }
    public function store(StoreFavouriteRequest $request,$favouriteable_type,$favouriteable_id){
        return $this->FavouriteRepository->store($request,$favouriteable_type,$favouriteable_id);
    }
    public function destroy($favouriteable_type,$favouriteable_id){
        return $this->FavouriteRepository->destroy($favouriteable_type ,$favouriteable_id);
    }
    



}
