<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFavouriteRequest;
use App\Services\FavouriteService;
use Illuminate\Http\Request;



class FavouritesController extends Controller
{
    protected $FavouriteService;
    public function __construct(FavouriteService $FavouriteService){
        $this->FavouriteService=$FavouriteService;
    }

    
    public function index(Request $request){
        $favourites=$request->filled('type')
        ? $this->FavouriteService->filterFavouriteByType($request->type)
        : $this->FavouriteService->index();

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
    public function destroy(int $favourite_id){
        $this->FavouriteService->destroy($favourite_id);
        return response()->json([
            'success'=>true,
            'message'=>'Favourite deleted successfully.'
        ],200);
    }

}
