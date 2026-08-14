<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface ProfileRepositoryInterface
{
    public function getProfile(User $user);

    public function updateProfile(User $user, array $data);

    public function updatePassword(User $user, array $data);
}
