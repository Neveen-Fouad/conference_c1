<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Services\NotificationService;
use App\Services\ReviewServiceNew;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $reviewService;

    protected $notificationService;

    public function __construct(ReviewServiceNew $reviewService, NotificationService $notificationService)
    {
        $this->reviewService = $reviewService;
        $this->notificationService = $notificationService;
    }

    public function UserIndex(Request $request)
    {
        if ($request->filled('type') && $request->filled('reviewable_id')) {
            $reviews = $this->reviewService->filterReviewByType(
                $request->type,
                $request->reviewable_id
            );
        } else {
            $reviews = $this->reviewService->index();
        }

        return response()->json([
            'success' => true,
            'message' => 'Reviews retrieved successfully.',
            'data' => $reviews,
        ], 200);
    }

    public function show(int $review_id)
    {
        $review = $this->reviewService->show($review_id);

        return response()->json([
            'success' => true,
            'data' => $review,
        ], 200);
    }

    public function store(StoreReviewRequest $request)
    {
        $validated = $request->validated();
        // if ($request->hasFile('image')) {
        //     $validated['image'] = $request->file('image')->store('reviews', 'public');
        // }
        try {
            $review = $this->reviewService->store($validated);

            $this->notificationService->createNotification([
                'client_id' => $review->client_id,
                'type' => 'review_submitted',
                'description' => 'Your review was submitted successfully and is pending approval.',
            ]);

            return response()->json([
                'success' => true,
                'data' => $review,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(UpdateReviewRequest $request, int $review_id)
    {
        $validated = $request->validated();
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('reviews', 'public');
        }
        $review = $this->reviewService->update($review_id, $validated);

        return response()->json([
            'success' => true,
            'data' => $review,
        ], 200);

    }

    public function destroy(int $review_id)
    {
        $this->reviewService->destroy($review_id);

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully.',
        ], 200);
    }

    public function getMyReviews()
    {
        $reviews = $this->reviewService->getMyReviews();

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ], 200);

    }

    public function AdminIndex(Request $request)
    {
        if ($request->filled('type') && $request->filled('reviewable_id')) {
            $reviews = $this->reviewService->filterReviewByType(
                $request->type,
                $request->reviewable_id
            );

        } elseif ($request->filled('status')) {
            $reviews = $this->reviewService->filterReviewByStatus($request->status);

        } else {
            $reviews = $this->reviewService->index();
        }

        return response()->json([
            'success' => true,
            'message' => 'Reviews retrieved successfully.',
            'data' => $reviews,
        ], 200);

    }

    public function approve(int $review_id)
    {
        $review = $this->reviewService->approveReview($review_id);

        $this->notificationService->createNotification([
            'client_id' => $review->client_id,
            'type' => 'review_approved',
            'description' => 'Your review has been approved.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review approved successfully.',
            'data' => $review,
        ], 200);
    }

    public function reject(int $review_id)
    {
        $review = $this->reviewService->rejectReview($review_id);

        $this->notificationService->createNotification([
            'client_id' => $review->client_id,
            'type' => 'review_rejected',
            'description' => 'Your review has been rejected.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review rejected successfully.',
            'data' => $review,
        ], 200);
    }
}
