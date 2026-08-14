<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function register(array $data);

    public function login(array $data);

    public function logout();

    public function verifyEmail(User $user);

    public function resendVerificationEmail(User $user): void;

    public function forgotPassword(array $data);

    public function resetPassword(array $data);

    public function refresh();
}
