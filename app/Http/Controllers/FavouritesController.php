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
    public function destroy($type,$favouriteable_id){
         $this->FavouriteService->destroy($type ,$favouriteable_id);
         return response()->json([
            'success'=>true,
            'message'=>'Favourite deleted successfully.'
         ]);
    }
    



}
