<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Drug;
use App\Models\Nurse;
use App\Models\ServiceType;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TransactionRepository
{
    function __construct(
    ) {}

    private function getQuery($data = null)
    {
        $model = Transaction::query();

        $qWord = Arr::get($data, 'q');
        if (!empty($qWord)) {
            $model->where(function ($query) use ($qWord) {
                $query->where('name', 'like', "%$qWord%")
                    ->orWhere('description', 'like', "%$qWord%")
                    ->orWhere('user_uuid', '=', "%$qWord%")
                    ->orWhere('patient_uuid', '=', "%$qWord%")
                    ->orWhere('service_date', '=', "%$qWord%")
                    ->orWhere('status', '=', "%$qWord%")
                    ->orWhere('total_amount', '=', "%$qWord%");
            });
        }

        $name = Arr::get($data, 'filter.name');
        if (!empty($name)) {
            $model->where('name', 'like', "%$name%");
        }

        $description = Arr::get($data, 'filter.description');
        if (!empty($description)) {
            $model->where('description', 'like', "%$description%");
        }

        $userUUID = Arr::get($data, 'filter.user_uuid');
        if (!empty($userUUID)) {
            $model->where('user_uuid', '=', "$userUUID");
        }

        $patientUUID = Arr::get($data, 'filter.patient_uuid');
        if (!empty($patientUUID)) {
            $model->where('patient_uuid', '=', "$patientUUID");
        }

        $serviceDate = Arr::get($data, 'filter.service_date');
        if (!empty($serviceDate)) {
            $model->where('service_date', '=', "$serviceDate");
        }

        $status = Arr::get($data, 'filter.status');
        if (!empty($status)) {
            $model->where('status', '=', "$status");
        }

        $totalAmount = Arr::get($data, 'filter.total_amount');
        if (!empty($totalAmount)) {
            $model->where('total_amount', '=', "$totalAmount");
        }

        $uuid = Arr::get($data, 'filter.uuid');
        if (!empty($uuid)) {
            $model->where('uuid', '=', "$uuid");
        }

        return $model;
    }

    public function browse($data = null)
    {
        $model = $this->getQuery($data);

        CommonHelper::sortPageFilter($model, $data);

        return $model->get();
    }

    public function find($uuid)
    {
        $role = Transaction::where('uuid', $uuid)
            ->first();

        return $role;
    }

    public function findByName($name)
    {
        $role = Transaction::where('name', $name)
            ->first();

        return $role;
    }

    public function add($data)
    {
        DB::beginTransaction();
        try {
            $transaction = new Transaction();
            $transaction->name = Arr::get($data, 'name');
            $transaction->description = Arr::get($data, 'description');
            $transaction->user_uuid = Arr::get($data, 'user_uuid');
            $transaction->patient_uuid = Arr::get($data, 'patient_uuid');
            $transaction->service_date = Arr::get($data, 'service_date');
            $transaction->status = Transaction::STATUS_PENDING;
            $transaction->total_amount = 0;
            $transaction->save();

            $totalAmount = 0;
            $details = Arr::get($data, 'details');
            foreach ($details as $detail) {
                $item_type = Arr::get($detail, 'item_type');
                $item_uuid = Arr::get($detail, 'item_uuid');
                $itemDetail = $this->getTransactionDetailItem($item_type, $item_uuid);
                $quantity = Arr::get($detail, 'quantity');
                $subTotal = $quantity * $itemDetail->price;

                $transactionDetail = new TransactionDetail();
                $transactionDetail->transaction_uuid = $transaction->uuid;
                $transactionDetail->name = $itemDetail->name;
                $transactionDetail->status = Transaction::STATUS_PENDING;
                $transactionDetail->quantity = $quantity;
                $transactionDetail->unit_price = $itemDetail->price;
                $transactionDetail->sub_total = $subTotal;
                $transactionDetail->item_type = Arr::get($detail, 'item_type');
                $transactionDetail->item_uuid = Arr::get($detail, 'item_uuid');

                $transactionDetail->save();

                $totalAmount += $subTotal;
            }

            $transaction->total_amount = $totalAmount;
            $transaction->update();

            DB::commit();

            return $transaction->find($transaction->uuid);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getTransactionDetailItem($item_type, $item_uuid) {
        if ($item_type === 'service') {
            $service = ServiceType::findOrFail($item_uuid);
            return $service;
        }

        if ($item_type === 'drug') {
            $drug = Drug::findOrFail($item_uuid);
            return $drug;
        }

        return null;
    }

    public function update($uuid, $data)
    {
        $model = Transaction::findOrFail($uuid);
        $model->update($data);

        $model->fresh();
        return $model;
    }

    public function delete(Transaction $transaction)
    {
        return $transaction->delete();
    }

    public function count($data)
    {
        $model = $this->getQuery($data);
        return $model->count();
    }
}
