<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Drug;
use Illuminate\Support\Arr;

class DrugRepository
{
    function __construct(
    ) {}

    private function getQuery($data = null)
    {
        $model = Drug::query();

        $qWord = Arr::get($data, 'q');
        if (!empty($qWord)) {
            $model->where(function ($query) use ($qWord) {
                $query->where('service_types.name', 'like', "%$qWord%");
                $query->orWhere('service_types.description', 'like', "%$qWord%");
                $query->orWhere('service_types.price', 'like', "%$qWord%");
            });
        }

        $name = Arr::get($data, 'filter.name');
        if (!empty($name)) {
            $model->where('service_types.name', 'like', "%$name%");
        }

        $uuid = Arr::get($data, 'filter.uuid');
        if (!empty($uuid)) {
            $model->where('service_types.uuid', '=', "$uuid");
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
        $drug = Drug::where('uuid', $uuid)
            ->first();

        return $drug;
    }

    public function findByName($name)
    {
        $drug = Drug::where('name', $name)
            ->first();

        return $drug;
    }

    public function add($data)
    {
        $model = new Drug();
        $model->name = Arr::get($data, 'name');
        $model->description = Arr::get($data, 'description');
        $model->price = Arr::get($data, 'price');
        $model->save();

        return $model;
    }

    public function update($uuid, $data)
    {

        $model = Drug::findOrFail($uuid);
        $model->update($data);

        $model->fresh();
        return $model;
    }

    public function delete(Drug $drug)
    {
        return $drug->delete();
    }

    public function count($data)
    {
        $model = $this->getQuery($data);
        return $model->count();
    }
}
