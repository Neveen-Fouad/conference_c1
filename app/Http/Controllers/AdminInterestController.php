<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInterestRequest;
use App\Http\Requests\UpdateInterestRequest;
use App\Interfaces\InterestRepositoryInterface;

class AdminInterestController extends Controller
{
    public function __construct(
        protected InterestRepositoryInterface $InterestRepository
    ) {
        $this->InterestRepository = $InterestRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json($this->InterestRepository->getAll());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInterestRequest $request)
    {
        $interest = $this->InterestRepository->create($request->validated());

        return response()->json([
            'message' => 'Interest created successfully',
            'data' => $interest,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInterestRequest $request, int $interest_id)
    {
        $interest = $this->InterestRepository->update($interest_id, $request->validated());

        return response()->json([
            'message' => 'Interest updated successfully',
            'data' => $interest,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $interest_id)
    {
        $this->InterestRepository->delete($interest_id);

        return response()->json([
            'message' => 'Interest deleted successfully.',
        ]);
    }
}
