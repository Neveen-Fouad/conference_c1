<?php
namespace App\Interfaces;

interface UserRepositoryInterface extends BaseRepositoryInterface{
    public function changeStatus($id, $status);

    public function createAdmin(array $data);
}
