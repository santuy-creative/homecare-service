<?php

namespace App\Http\Controllers\Api;

use App\Constants\PaymentMethodResponse;
use App\Constants\Pagination;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Repositories\PaymentMethodRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
    protected $repository;
    protected $roleUserRepository;
    public function __construct(
        PaymentMethodRepository $repository,
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
                'page' => 'nullable|integer',
                'limit' => 'nullable|integer',
                'sortOrder' => sprintf('nullable|string|in:%s,%s', Pagination::ASC_PARAM, Pagination::DESC_PARAM),
                'sortField' => 'nullable|string',
            ])->safe()->all();

            $paymentMethods = $this->repository->browse($validator);
            $totalPaymentMethod = $this->repository->count($validator);

            return response()->json([
                'status' => PaymentMethodResponse::SUCCESS,
                'message' => PaymentMethodResponse::SUCCESS_ALL_RETRIEVED,
                'data' => $paymentMethods,
                'total' => $totalPaymentMethod,
            ]);

        } catch (\Throwable $th) {
            $errMessage = $th->getMessage();
            $errCode = CommonHelper::getStatusCode($errMessage);

            return response()->json([
                'status' => PaymentMethodResponse::ERROR,
                'message' => $errMessage,
            ], $errCode);
        }
    }

    public function show($uuid)
    {
        $paymentMethod = $this->repository->find($uuid);

        if(empty($paymentMethod)) {
            return response()->json([
                'status' => PaymentMethodResponse::SUCCESS,
                'message' => PaymentMethodResponse::NOT_FOUND,
                'error' => true,
                'data' => [],
            ], 404);
        }

        return response()->json([
            'status' => PaymentMethodResponse::SUCCESS,
            'message' => PaymentMethodResponse::SUCCESS_RETRIEVED,
            'data' => $paymentMethod,
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
            ];

            $customMessages = [
                'name.unique' => 'name must be unique.',
            ];

            $validator = Validator::make($data, $rules, $customMessages);

            $validator->validate();
            $validator = $validator->safe()->all();

            $paymentMethod = $this->repository->add($validator);

            return response()->json([
                'status' => PaymentMethodResponse::SUCCESS,
                'message' => PaymentMethodResponse::SUCCESS_CREATED,
                'data' => $paymentMethod,
            ], 201);
        } catch (\Throwable $th) {
            $errMessage = $th->getMessage();
            $errCode = CommonHelper::getStatusCode($errMessage);

            return response()->json([
                'status' => PaymentMethodResponse::ERROR,
                'message' => $errMessage,
            ], $errCode);
        }
    }

    public function update($uuid, Request $request)
    {
        try {
            $paymentMethod = $this->repository->find($uuid);
            if (empty($paymentMethod)) {
                return response()->json([
                    'status' => PaymentMethodResponse::SUCCESS,
                    'message' => PaymentMethodResponse::NOT_FOUND,
                    'data' => $paymentMethod,
                ], 201);
            }

            $validator = Validator::make($request->all(), [
                'name'      => "required|string|unique:payment_methods",
            ]);

            $validator->validate();
            $validator = $validator->safe()->all();

            $paymentMethod = $this->repository->update($uuid, $validator);

            return response()->json([
                'status' => PaymentMethodResponse::SUCCESS,
                'message' => PaymentMethodResponse::SUCCESS_UPDATED,
                'data' => $paymentMethod,
            ], 201);
        } catch (\Throwable $th) {
            $errMessage = $th->getMessage();
            $errCode = CommonHelper::getStatusCode($errMessage);

            return response()->json([
                'status' => PaymentMethodResponse::ERROR,
                'message' => $errMessage,
            ], $errCode);
        }
    }

    public function destroy($uuid)
    {
        try {
            $paymentMethod = $this->repository->find($uuid);

            $this->repository->delete($paymentMethod);

            return response()->json([
                'status' => PaymentMethodResponse::SUCCESS,
                'message' => PaymentMethodResponse::SUCCESS_DELETED,
            ]);
        } catch (\Throwable $th) {
            $errMessage = $th->getMessage();
            $errCode = CommonHelper::getStatusCode($errMessage);

            return response()->json([
                'status' => PaymentMethodResponse::ERROR,
                'message' => $errMessage,
            ], $errCode);
        }
    }
}
