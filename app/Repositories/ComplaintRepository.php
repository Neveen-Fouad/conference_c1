<?php

namespace App\Repositories;

use App\Models\Complaint;
use App\Interfaces\ComplaintRepositoryInterface;

class ComplaintRepository extends BaseRepository implements ComplaintRepositoryInterface
{
    public function __construct(Complaint $complaint)
    {
        parent::__construct($complaint);
    }

    public function changeStatus($id, $status)
    {
        $complaint = $this->findById($id);

        $complaint->status = $status;
        $complaint->save();

        return $complaint;
    }
}