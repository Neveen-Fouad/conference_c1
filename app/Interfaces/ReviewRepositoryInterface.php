<?php
namespace App\Interfaces;
use App\Interfaces\BaseRepositoryInterface;
interface ReviewRepositoryInterface extends BaseRepositoryInterface
{
    public function addReview(array $data , $client_id);

    public function getMyReviews(int $client_id);

    public function filterReviewsByType(int $client_id,int $user_id,string $type);

    public function removeReview(int $user_id, int $client_id,string $type ,string $parameter);

    public function getAllReviews(int $user_id);

}