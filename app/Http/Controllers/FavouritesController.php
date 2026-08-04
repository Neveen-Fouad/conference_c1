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
        if($request->filled('type')){
            $favourites=$this->FavouriteService->filter($request->type);
        }else{
            $favourites=$this->FavouriteService->index();
        }
         return response()->json([
            'success'=>true,
            'message'=>'Favourites retrieved successfully',
            'data'=>$favourites,
            ],200);

    }
    public function store(StoreFavouriteRequest $request){
        $favourite=$this->FavouriteService->store($request->validated());
         return response()->json([
            'success'=>true,
            'data'=>$favourite,
            ],201);
        

    }
    public function destroy($favouriteable_type,$favouriteable_id){
         $this->FavouriteService->destroy($favouriteable_type ,$favouriteable_id);
         return response()->json([
            'success'=>true,
            'message'=>'Favourite deleted successfully.'
         ]);
    }
    



}
