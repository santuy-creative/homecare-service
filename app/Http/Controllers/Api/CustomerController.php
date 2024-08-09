<?php

namespace App\Http\Controllers\Api;

use App\Constants\ProfileResponse;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Repositories\CustomerRepository;
use App\Repositories\RoleUserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

class CustomerController extends Controller
{
    protected $repository;
    protected $roleRepository;

    public function __construct(
        CustomerRepository $repository,
        RoleUserRepository $roleRepository,
    )
    {
        $this->repository = $repository;
        $this->roleRepository = $roleRepository;
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $userUuid = $user->uuid;

        try {
            $profile = $this->repository->find($userUuid);

            if(!empty($profile)) {
                $validator = Validator::make($request->all(), [
                    'firstname' => 'sometimes|string',
                    'lastname'  => 'sometimes|string',
                    'birthdate' => 'sometimes|date',
                    'phone'     => "sometimes|string|unique:profiles,phone,$userUuid,user_uuid",
                    'bio'       => 'sometimes|string',
                    'address'   => 'sometimes|string',
                ]);
                $validator->validate();
                $validator = $validator->safe()->all();
                Arr::set($validator, 'user_uuid', $userUuid);
                $updatedProfile = $this->repository->update($profile, $validator);

                return response()->json([
                    'status' => ProfileResponse::SUCCESS,
                    'message' => ProfileResponse::SUCCESS_UPDATED,
                    'data' => $updatedProfile,
                ], 201);
            }

            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string',
                'lastname'  => 'required|string',
                'birthdate' => 'required|date',
                'phone'     => 'required|string|unique:profiles',
                'bio'       => 'required|string',
                'address'       => 'required|string',
            ]);

            $validator->validate();
            $validator = $validator->safe()->all();
            Arr::set($validator, 'user_uuid', $userUuid);

            $profile = $this->repository->add($validator);

            return response()->json([
                'status' => ProfileResponse::SUCCESS,
                'message' => ProfileResponse::SUCCESS_CREATED,
                'data' => $profile,
            ], 201);
        } catch (\Throwable $th) {
            $errMessage = $th->getMessage();
            $errCode = CommonHelper::getStatusCode($errMessage);

            return response()->json([
                'status' => ProfileResponse::ERROR,
                'message' => $errMessage,
            ], $errCode);
        }
    }
}
