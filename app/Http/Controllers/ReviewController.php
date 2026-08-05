<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\services\ReviewService; 
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $reviewService;
    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }
    public function index (Request $request){
        

    }
    public function show(int $review_id){
        $review = $this->reviewService->show($review_id);
        return response()->json([
            'success'=>true,
            'data' => $review,
        ],200);
    }
    public function store(StoreReviewRequest $request){
        $review=$this->reviewService->store($request->validated());
        return response()->json([
            'success'=>true,
            'data'=>$review,
        ],201);
    }
    public function update(UpdateReviewRequest $request , int $review_id){
        $review = $this->reviewService->update($review_id,$request->validated());
        return response()->json([
            'success'=>true,
            'data'=>$review,
        ],200);

    }
    public  function destroy(int $review_id){
        $this->reviewService->destroy($review_id);
        return response()->json([
            'success'=>true,
            'message'=>'Review deleted successfully.'
        ],200);
    }
    public function getMyReviews(){
        $reviews=$this->reviewService->getMyReviews();
        return response()->json([
            'success'=>true,
            'data'=>$reviews,
        ],200);

    }
    public function approve(int $review_id){
        $review=$this->reviewService->approveReview($review_id);
        return response()->json([
            'success'=>true,
            'message'=>'Review approved successfully.',
            'data'=>$review,
        ],200);
    }
    public function reject(int $review_id){
        $review=$this->reviewService->rejectReview($review_id);
        return response()->json([
            'success'=>true,
            'message'=>'Review rejected successfully.',
            'data'=>$review,
        ],200);
    }

}
