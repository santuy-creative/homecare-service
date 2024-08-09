<?php

namespace App\Http\Controllers\Api;

use App\Constants\DrugResponse;
use App\Constants\Pagination;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Repositories\DrugRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DrugController extends Controller
{
    protected $repository;
    public function __construct(
        DrugRepository $repository,
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
                'filter.description' => 'nullable|string',
                'filter.price' => 'nullable|numeric',
                'page' => 'nullable|integer',
                'limit' => 'nullable|integer',
                'sortOrder' => sprintf('nullable|string|in:%s,%s', Pagination::ASC_PARAM, Pagination::DESC_PARAM),
                'sortField' => 'nullable|string',
            ])->safe()->all();

            $grades = $this->repository->browse($validator);
            $totalRoles = $this->repository->count($validator);

            return response()->json([
                'status' => DrugResponse::SUCCESS,
                'message' => DrugResponse::SUCCESS_ALL_RETRIEVED,
                'data' => $grades,
                'total' => $totalRoles,
            ]);

        } catch (\Throwable $th) {
            $errMessage = $th->getMessage();
            $errCode = CommonHelper::getStatusCode($errMessage);

            return response()->json([
                'status' => DrugResponse::ERROR,
                'message' => $errMessage,
            ], $errCode);
        }
    }

    public function show($uuid)
    {
        $grade = $this->repository->find($uuid);

        if(empty($grade)) {
            return response()->json([
                'status' => DrugResponse::SUCCESS,
                'message' => DrugResponse::NOT_FOUND,
                'error' => true,
                'data' => [],
            ], 404);
        }

        return response()->json([
            'status' => DrugResponse::SUCCESS,
            'message' => DrugResponse::SUCCESS_RETRIEVED,
            'data' => $grade,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();

            $rules = [
                'name' => [
                    'required',
                    'string',
                ],
                'description' => [
                    'required',
                    'string',
                ],
                'price' => [
                    'required',
                    'numeric',
                ],
            ];

            $validator = Validator::make($data, $rules);

            $validator->validate();
            $validator = $validator->safe()->all();

            $grade = $this->repository->add($validator);

            return response()->json([
                'status' => DrugResponse::SUCCESS,
                'message' => DrugResponse::SUCCESS_CREATED,
                'data' => $grade,
            ], 201);
        } catch (\Throwable $th) {
            $errMessage = $th->getMessage();
            $errCode = CommonHelper::getStatusCode($errMessage);

            return response()->json([
                'status' => DrugResponse::ERROR,
                'message' => $errMessage,
            ], $errCode);
        }
    }

    public function update($uuid, Request $request)
    {
        try {
            $grade = $this->repository->find($uuid);
            if (empty($grade)) {
                return response()->json([
                    'status'  => DrugResponse::SUCCESS,
                    'message' => DrugResponse::NOT_FOUND,
                    'data'    => $grade,
                ], 201);
            }

            $rules = [
                'name' => [
                    'sometimes',
                    'string',
                ],
                'description' => [
                    'required',
                    'string',
                ],
                'price' => [
                    'sometimes',
                    'numeric',
                ],
            ];

            $validator = Validator::make($request->all(), $rules);

            $validator->validate();
            $validator = $validator->safe()->all();

            $grade = $this->repository->update($uuid, $validator);

            return response()->json([
                'status' => DrugResponse::SUCCESS,
                'message' => DrugResponse::SUCCESS_UPDATED,
                'data' => $grade,
            ], 201);
        } catch (\Throwable $th) {
            $errMessage = $th->getMessage();
            $errCode = CommonHelper::getStatusCode($errMessage);

            return response()->json([
                'status' => DrugResponse::ERROR,
                'message' => $errMessage,
            ], $errCode);
        }
    }

    public function destroy($uuid)
    {
        try {
            $grade = $this->repository->find($uuid);

            $this->repository->delete($grade);

            return response()->json([
                'status' => DrugResponse::SUCCESS,
                'message' => DrugResponse::SUCCESS_DELETED,
            ]);
        } catch (\Throwable $th) {
            $errMessage = $th->getMessage();
            $errCode = CommonHelper::getStatusCode($errMessage);

            return response()->json([
                'status' => DrugResponse::ERROR,
                'message' => $errMessage,
            ], $errCode);
        }
    }
}
