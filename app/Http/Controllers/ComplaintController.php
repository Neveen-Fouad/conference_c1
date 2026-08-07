<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComplaintsRequest;
use Illuminate\Http\Request;
use App\Interfaces\ComplaintRepositoryInterface;
use App\Http\Requests\UpdateComplaintStatusRequest;

class ComplaintController extends Controller
{
    //
    protected  $ComplaintRepository;
     public function __construct(ComplaintRepositoryInterface $complaintRepository)
    {
        $this->ComplaintRepository = $complaintRepository;
    }

    public function store(StoreComplaintsRequest $request){
        return response()->json(
            $this->ComplaintRepository->create($request->validated())
        );
    }

    //contact messages get 
    public function index(){
        return response()->json(
            $this->ComplaintRepository->getAll()
        );
    }

    //delete contact messages 
    public function destroy(int $id){
        return response()->json(
             $this->ComplaintRepository->delete($id)
        );
        
    }

    // mark as read
    public function changeStatus( UpdateComplaintStatusRequest $request, int $id)
    {
        return response()->json(
            $this->ComplaintRepository->changeStatus(
                $id,
                $request->status
            )
        );
}
}
