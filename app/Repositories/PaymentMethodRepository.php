<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\PaymentMethod;
use Illuminate\Support\Arr;

class PaymentMethodRepository
{
    function __construct(
    ) {}

    private function getQuery($data = null)
    {
        $model = PaymentMethod::query();

        $qWord = Arr::get($data, 'q');
        if (!empty($qWord)) {
            $model->where(function ($query) use ($qWord) {
                $query->where('name', 'like', "%$qWord%")
                    ->orWhere('description', 'like', "%$qWord%");
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
        $role = PaymentMethod::where('uuid', $uuid)
            ->first();

        return $role;
    }

    public function findByName($name)
    {
        $role = PaymentMethod::where('name', $name)
            ->first();

        return $role;
    }

    public function add($data)
    {
        $totalRole = PaymentMethod::count();

        $model = new PaymentMethod();
        $model->name = Arr::get($data, 'name');
        $model->description = Arr::get($data, 'description');
        $model->save();

        return $model;
    }

    public function update($uuid, $data)
    {
        $model = PaymentMethod::findOrFail($uuid);
        $model->update($data);

        $model->fresh();
        return $model;
    }

    public function delete(PaymentMethod $paymentMethod)
    {
        return $paymentMethod->delete();
    }

    public function count($data)
    {
        $model = $this->getQuery($data);
        return $model->count();
    }
}
