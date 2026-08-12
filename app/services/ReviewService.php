<?php
namespace App\Services;

use App\Enum\ReviewType;
use App\Interfaces\ReviewRepositoryInterface;
use App\Models\trip;
use App\Models\bookings;
use App\Services\NotificationService;
use App\Models\Client;


class ReviewService
{
    protected $reviewRepository;
    protected $hotelService;
    protected $restaurantService;
    protected $flightService;



    protected function hasQualifyingBooking(array $data): ?bookings
{
    return bookings::where('client_id', $data['client_id'])
        ->where('type', $data['type'])
        ->where('external_reference_id', (string) $data['reviewable_id'])
        ->whereDate('check_out_date', '<', now()) 
        ->whereDoesntHave('review')
        ->latest()
        ->first();
}
    public function __construct(
    ReviewRepositoryInterface $reviewRepository,
    HotelService $hotelService,
    RestaurantService $restaurantService,
    FlightService $flightService,
    NotificationService $notificationService
){
    $this->reviewRepository = $reviewRepository;
    $this->hotelService = $hotelService;
    $this->restaurantService = $restaurantService;
    $this->flightService = $flightService;
    $this->notificationService = $notificationService;
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

    $booking = $this->hasQualifyingBooking($data);
    if (! $booking) {
        throw new \InvalidArgumentException('You can only review items you have booked.');
    }

    $data['booking_id'] = $booking->id;

    return $this->reviewRepository->create($data);
}
    public function update(int $review_id , array $data)
    {
        return $this->reviewRepository->update($review_id,$data);
    }
    public function destroy(int $review_id)
    {
        return $this->reviewRepository->delete($review_id);
    }
    public function getMyReviews()
    {
        return $this->reviewRepository->getMyReviews();
    }
    public function filterReviewByType(string $type , int $reviewable_id)
    {
        return $this->reviewRepository->filterReviewsByType($type,$reviewable_id);
    }
    public function filterReviewByStatus(string $status)
    {
        return $this->reviewRepository->filterReviewsByStatus($status);
    }
    public function approveReview(int $review_id)
{
    $review = $this->reviewRepository->approveReview($review_id);

    $client = Client::find($review->client_id);
    if ($client) {
        $this->notificationService->sendReviewApprovedNotification($client);
    }

    return $review;
}

public function rejectReview(int $review_id)
{
    $review = $this->reviewRepository->rejectReview($review_id);

    $client = Client::find($review->client_id);
    if ($client) {
        $this->notificationService->sendReviewRejectedNotification($client);
    }

    return $review;
}

    
    protected function reviewableExists(string $type , int $reviewable_id):bool
    {
        return match ($type){
            ReviewType::Trip->value => trip::find($reviewable_id) !== null,
            ReviewType::Hotel->value => $this->hotelService->getHotelDetails((string) $reviewable_id) !== null,
            ReviewType::Restaurant->value => $this->restaurantService->getRestaurantDetails((string) $reviewable_id) !==null,
            ReviewType::Flight->value => $this->flightService->getFlightDetails((string) $reviewable_id) !== null,
        };

    }
    protected $notificationService;



}