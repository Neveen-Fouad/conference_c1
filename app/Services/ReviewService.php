<?php

namespace App\Services;

use App\Enum\ReviewType;
use App\Interfaces\ReviewRepositoryInterface;
use App\Models\Trip;

class ReviewService
{
    protected $reviewRepository;

    protected $hotelService;

    protected $restaurantService;

    protected $flightService;

    public function __construct(
        ReviewRepositoryInterface $reviewRepository,
        HotelService $hotelService,
        RestaurantService $restaurantService,
        FlightService $flightService
    ) {
        $this->reviewRepository = $reviewRepository;
        $this->hotelService = $hotelService;
        $this->restaurantService = $restaurantService;
        $this->flightService = $flightService;
    }

    public function index()
    {
        return $this->reviewRepository->getAll();
    }

    public function show(int $review_id)
    {
        return $this->reviewRepository->findById($review_id);
    }

    public function store(array $data)
    {
        $exists = $this->reviewableExists($data['type'], $data['reviewable_id']);
        if (! $exists) {
            throw new \InvalidArgumentException('The item being reviewed does not exist.');
        }

        return $this->reviewRepository->create($data);
    }

    public function update(int $review_id, array $data)
    {
        return $this->reviewRepository->update($review_id, $data);
    }

    public function destroy(int $review_id)
    {
        return $this->reviewRepository->delete($review_id);
    }

    public function getMyReviews()
    {
        return $this->reviewRepository->getMyReviews();
    }

    public function filterReviewByType(string $type, string $reviewable_id)
    {
        return $this->reviewRepository->filterReviewsByType($type, $reviewable_id);
    }

    public function filterReviewByStatus(string $status)
    {
        return $this->reviewRepository->filterReviewsByStatus($status);
    }

    public function approveReview(int $review_id)
    {
        return $this->reviewRepository->approveReview($review_id);
    }

    public function rejectReview(int $review_id)
    {
        return $this->reviewRepository->rejectReview($review_id);
    }

    protected function reviewableExists(string $type, string $reviewable_id): bool
    {
        return match ($type) {
            ReviewType::Trip->value => ctype_digit($reviewable_id) && Trip::find((int) $reviewable_id) !== null,
            ReviewType::Hotel->value,
            ReviewType::Restaurant->value,
            ReviewType::Flight->value => trim($reviewable_id) !== '',
            default => false,
        };

    }
}
