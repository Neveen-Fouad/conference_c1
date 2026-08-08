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

    public function statistics()
{
    return [
        'total_users' => User::count(),

        'monthly_users' => User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count(),

        'verified_users' => User::whereNotNull('email_verified_at')->count(),

        'unverified_users' => User::whereNull('email_verified_at')->count(),
    ];
}
}