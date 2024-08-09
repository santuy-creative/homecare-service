<?php

namespace App\Http\Controllers\Api;

use App\Constants\Pagination;
use App\Constants\UserResponse;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $repository;
    public function __construct(
        UserRepository $repository
    )
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'q' => 'nullable|string',
                'filter.name' => 'nullable|string',
                'filter.email' => 'nullable|string',
                'page' => 'nullable|integer',
                'limit' => 'nullable|integer',
                'sortOrder' => sprintf('nullable|string|in:%s,%s', Pagination::ASC_PARAM, Pagination::DESC_PARAM),
                'sortField' => 'nullable|string',
            ])->safe()->all();

            $users = $this->repository->browse($validator);
            $totalUsers = $this->repository->count($validator);

            return response()->json([
                'status' => UserResponse::SUCCESS,
                'message' => UserResponse::SUCCESS_ALL_RETRIEVED,
                'data' => $users,
                'total' => $totalUsers,
            ]);
        } catch (\Throwable $th) {
            $errMessage = $th->getMessage();
            $errCode = CommonHelper::getStatusCode($errMessage);

            return response()->json([
                'status' => UserResponse::ERROR,
                'message' => $errMessage,
            ], $errCode);
        }
    }
}
