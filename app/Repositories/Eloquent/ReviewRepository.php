<?php
namespace App\Repositories;
use App\Enum\ReviewStatus;
use App\Interfaces\ReviewRepositoryInterface;
use App\Models\review;
use Illuminate\Auth\Access\AuthorizationException;

class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface
{
    public function __construct(review $model)
    {
        parent::__construct($model);
    }
    public function getAll()
    {
        
        return $this->model
        ->where('status', ReviewStatus::Approved->value)
        ->paginate(10);
    }

    public function findById($id)
    {
        $review = $this->model
            ->where('id', $id)
            ->where('client_id',auth('api')->id())
            ->firstOrFail();

        if($review->status !== ReviewStatus::Approved->value
        && $review->client_id !== auth('api')->id()){ {
            throw new AuthorizationException('You are not authorized to view this review.');
        }
        return $review;
     
    }
    }

    public function create(array $data)
    {
        $review = $this->model
        ->where('client_id', auth('api')->id())
        ->where('status', ReviewStatus::pending->value);
        return $this->model->create($data);
        
        
    }

    public function delete($id)
    {
        $review = $this->model
            ->where('client_id', auth('api')->id())
            ->firstOrFail($id);
        return $review->delete();
    }

    public function updateReview($id, array $data)
    {
        $review = $this->model
            ->where('client_id', auth('api')->id())
            ->firstOrFail($id);
        if($review->status !== ReviewStatus::pending->value) {
            throw new AuthorizationException('You can only update pending reviews.');
        }
        $review->update($data);
        return $review;
    }

    public function getMyReviews()
    {
        return $this->model
            ->where('client_id', auth('api')->id())
            ->paginate(10);
    }

    public function filterReviewsByType(string $type, int $reviewable_id)
    {
        return $this->model
            ->where('type', $type)
            ->where('reviewable_id', $reviewable_id)
            ->paginate(10);
    }

    public function adminIndex()
    {
        return $this->model
            ->paginate(10);
    }

    public function filterReviewsByStatus(string $status)
    {
        return $this->model
            ->where('status', $status)
            ->paginate(10);
    }

    public function approveReview(int $review_id)
    {
        $review = $this->model
            ->where('id', $review_id)
            ->firstOrFail();
        $review->update(['status' => ReviewStatus::Approved->value]);
        return $review;
        
    }

    public function rejectReview(int $review_id)
    {
        $review = $this->model
            ->where('id', $review_id)
            ->firstOrFail();
        $review->update(['status' => ReviewStatus::Rejected->value]);
        return $review;
    }

}