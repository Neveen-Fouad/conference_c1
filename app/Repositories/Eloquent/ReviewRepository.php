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
    protected function currentClientId(){
        return auth('api')->user()?->client?->id;
    }
    protected function isAdmin():bool{
        return auth('api')->user()?->role ==='admin';

    }
    
    public function getAll()
    {
        if($this->isAdmin()){
            return $this->model->paginate(10);
        }
        return $this->model
        ->where('status', ReviewStatus::Approved->value)
        ->paginate(10);
    }

    public function findById($id)
    {
        $review= $this->model->findOrFail($id);
        if(! $this->isAdmin()
        && $review->status !== ReviewStatus::Approved->value
        && $review->client_id !== $this->currentClientId()){
            throw new AuthorizationException('Not authorized to view this review.');
        }
        return $review;
    }

    public function create(array $data)
    {
        // 
        $data['client_id']=$this->currentClientId();
        $data['status']=ReviewStatus::pending->value;
        return $this->model->create($data);
        
        
    }

    public function delete($id)
    {
        $review = $this->model
            ->where('client_id', $this->currentClientId())
            ->firstOrFail($id);
        return $review->delete();
    }

    public function updateReview($id, array $data)
    {
        $review = $this->model
            ->where('client_id', $this->currentClientId())
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
            ->where('client_id', $this->currentClientId())
            ->paginate(10);
    }

    public function filterReviewsByType(string $type, int $reviewable_id)
    {
        $query =$this->model
        ->where('type', $type)
        ->where('reviewable_id', $reviewable_id);
        if($this->isAdmin()){
            return $query->paginate(10);
        }
        $client_id = $this->currentClientId();
        if($client_id){
            $query->where(function ($q) use ($client_id){
                $q->where('status',ReviewStatus::Approved->value)
                ->orWhere('client_id', $client_id);
            });
        }else{
            $query->where('status', ReviewStatus::Approved->value);
        }
        return $query->paginate(10);
        
    }

    public function filterReviewsByStatus(string $status)
    {
        return $this->model
            ->where('status', $status)
            ->paginate(10);
    }

    public function approveReview(int $review_id)
    {
        $review = $this->model->firstOrFail($review_id);
        $review->update(['status' => ReviewStatus::Approved->value]);
        return $review;
        
    }

    public function rejectReview(int $review_id)
    {
        $review = $this->model->firstOrFail($review_id);
        $review->update(['status' => ReviewStatus::Rejected->value]);
        return $review;
    }

}