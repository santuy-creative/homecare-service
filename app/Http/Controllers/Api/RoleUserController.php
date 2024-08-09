<?php

namespace App\Http\Controllers\Api;

use App\Constants\RoleResponse;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Repositories\RoleRepository;
use App\Repositories\RoleUserRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class RoleUserController extends Controller
{
    protected $repository;
    protected $roleRepository;
    protected $userRepository;

    public function __construct(
        RoleUserRepository $repository,
        RoleRepository $roleRepository,
        UserRepository $userRepository,
    )
    {
        $this->repository = $repository;
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
    }

    public function update($uuid, Request $request)
    {
        try {
            $userRole = $this->repository->findByUserUUID($uuid);
            if (empty($userRole)) {
                return response()->json([
                    'status' => RoleResponse::ERROR,
                    'message' => RoleResponse::NOT_FOUND,
                    'data' => $userRole,
                ], 201);
            }

            if (Role::ROLE_SUPER_ADMIN === $userRole->constant_value) {
                return response()->json([
                    'status' => RoleResponse::ERROR,
                    'message' => RoleResponse::UNABLE_CHANGE_ADMIN_ROLE,
                    'data' => $userRole,
                ], 201);
            }

            $data = $request->all();

            $rules = [
                'role_uuid'=> 'sometimes|string',
                'is_active'  => 'sometimes|integer',
                'is_confirmed'  => 'sometimes|integer',
            ];

            $validator = Validator::make($data, $rules);

            $validator->validate();
            $validator = $validator->safe()->all();

            $roleUuid = Arr::get($validator, 'role_uuid');
            $role = $this->roleRepository->find($roleUuid);

            if(empty($role)) {
                return response()->json([
                    'status' => RoleResponse::ERROR,
                    'message' => RoleResponse::NOT_FOUND,
                    'data' => $validator,
                ], 422);
            }

            $isAlreadyAssigned = RoleUserRepository::isAlreadyAssigned($validator);
            if ($isAlreadyAssigned) {
                return response()->json([
                    'status' => RoleResponse::ERROR,
                    'message' => RoleResponse::ALREADY_ASSIGNED,
                    'data' => $validator,
                ], 422);
            }

            $isAllowedChangeRoleAdmin = RoleUserRepository::isAllowedChangeRoleAdmin($userRole);
            if (!$isAllowedChangeRoleAdmin) {
                return response()->json([
                    'status' => RoleResponse::ERROR,
                    'message' => RoleResponse::UNABLE_CHANGE_ADMIN_ROLE,
                    'data' => $userRole,
                ], 201);
            }

            Arr::set($validator, 'user_uuid', $uuid);
            Arr::set($validator, 'constant_value', $role->constant_value);
            $roleUser = $this->repository->update($uuid, $validator);

            return response()->json([
                'status' => RoleResponse::SUCCESS,
                'message' => RoleResponse::SUCCESS_UPDATED,
                'data' => $roleUser,
            ], 201);
        } catch (\Throwable $th) {
            $errMessage = $th->getMessage();
            $errCode = CommonHelper::getStatusCode($errMessage);

            return response()->json([
                'status' => RoleResponse::ERROR,
                'message' => $errMessage,
            ], $errCode);
        }
    }
}
