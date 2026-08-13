<?php

namespace App\Repositories;

use App\Interfaces\ComplaintRepositoryInterface;
use App\Models\Complaint;

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
