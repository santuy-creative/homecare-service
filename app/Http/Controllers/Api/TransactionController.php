<?php

namespace App\Http\Controllers\Api;

use App\Constants\PaymentMethodResponse;
use App\Constants\Pagination;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class TransactionController extends Controller
{
    protected $repository;
    protected $roleUserRepository;
    public function __construct(
        TransactionRepository $repository,
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
                'filter.user_uuid' => 'nullable|string',
                'filter.patient_uuid' => 'nullable|string',
                'filter.service_date' => 'nullable|string',
                'filter.status' => 'nullable|string',
                'filter.total_amount' => 'nullable|string',
                'page' => 'nullable|integer',
                'limit' => 'nullable|integer',
                'sortOrder' => sprintf('nullable|string|in:%s,%s', Pagination::ASC_PARAM, Pagination::DESC_PARAM),
                'sortField' => 'nullable|string',
            ])->safe()->all();

            $transactions = $this->repository->browse($validator);
            $totalTransaction = $this->repository->count($validator);

            return response()->json([
                'status' => PaymentMethodResponse::SUCCESS,
                'message' => PaymentMethodResponse::SUCCESS_ALL_RETRIEVED,
                'data' => $transactions,
                'total' => $totalTransaction,
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
        $transaction = $this->repository->find($uuid);

        if(empty($transaction)) {
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
            'data' => $transaction,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $userUuid = $user->uuid;

        try {
            $data = $request->all();

            $rules = [
                'patient_uuid' => [
                    'required',
                    'string',
                ],
                'service_date' => [
                    'required',
                    'date',
                ],
                'service_type_uuid' => [
                    'required',
                    'string',
                ],
                'details' => 'required|array',
                'details.*.quantity' => 'required|integer|min:1',
                'details.*.item_type' => 'required|string|max:255',
                'details.*.item_uuid' => 'required|string|max:255',
            ];

            $transactionName = 'TRX' . '-' . date('dYm') . '-' . Str::uuid();

            $validator = Validator::make($data, $rules);

            $validator->validate();
            $validator = $validator->safe()->all();
            Arr::set($validator, 'name', $transactionName);
            Arr::set($validator, 'user_uuid', $userUuid);

            $transaction = $this->repository->add($validator);

            return response()->json([
                'status' => PaymentMethodResponse::SUCCESS,
                'message' => PaymentMethodResponse::SUCCESS_CREATED,
                'data' => $transaction,
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
}
