<?php

namespace App\Interfaces;

interface ComplaintRepositoryInterface extends BaseRepositoryInterface
{
    public function changeStatus($id, $status);
}