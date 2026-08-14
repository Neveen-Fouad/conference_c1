<?php

namespace App\Interfaces;

interface ReviewRepositoryInterface extends BaseRepositoryInterface
{
    public function getMyReviews();

    public function filterReviewsByType(string $type, string $reviewable_id);

    public function filterReviewsByStatus(string $status);

    public function approveReview(int $review_id);

    public function rejectReview(int $review_id);
}
