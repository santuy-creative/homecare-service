<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Transaction;
use Illuminate\Support\Arr;

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
        $model = new Transaction();
        $model->name = Arr::get($data, 'name');
        $model->description = Arr::get($data, 'description');
        $model->user_uuid = Arr::get($data, 'user_uuid');
        $model->patient_uuid = Arr::get($data, 'patient_uuid');
        $model->service_date = Arr::get($data, 'service_date');
        $model->status = Arr::get($data, 'status');
        $model->total_amount = Arr::get($data, 'total_amount');
        $model->save();

        return $model;
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
