<?php

namespace App\Http\Controllers;

use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Requests\UpdateUserStatusRequest;
class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function index()
{
    return response()->json(
        $this->userRepository->getAll()
    );
}
public function show($id)
{
    return response()->json(
        $this->userRepository->findById($id)
    );
}
public function changeStatus(UpdateUserStatusRequest $request, $id)
{
    return response()->json(
        $this->userRepository->changeStatus(
            $id,
            $request->is_active
        )
    );
}
public function storeAdmin(StoreAdminRequest $request)
{
    return response()->json(
        $this->userRepository->createAdmin(
            $request->all()
        )
    );
}

public function updateAdmin(UpdateAdminRequest $request, $id)
{
    return response()->json(
        $this->userRepository->update(
            $id,
            $request->all()
        )
    );
}
public function destroyAdmin($id)
{
    $this->userRepository->delete($id);

    return response()->json([
        'message' => 'Admin deleted successfully'
    ]);
}

public function statistics()
{
    return response()->json(
        $this->userRepository->statistics()
    );
}
}