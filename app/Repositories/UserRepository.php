<?php
namespace App\Repositories;

use App\Models\User;
use App\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function changeStatus($id, $status){
        $user= $this->findById($id);

        $user->is_active =$status;
        $user->save();
        return $user;
    }

    public function createAdmin(array $data)
    {
        $data['role'] = 'admin';

        return $this->create($data);
    }
}