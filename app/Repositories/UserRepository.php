<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Customer;
use App\Models\MedicalPerson;
use App\Models\Nurse;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    function __construct(
    ) {}

    private function getQuery($data = null)
    {
        $model = User::query()->select('uuid', 'name', 'email', 'created_at', 'updated_at');

        $qWord = Arr::get($data, 'q');
        if (!empty($qWord)) {
            $model->where(function ($query) use ($qWord) {
                $query->where('name', 'like', "%$qWord%")
                    ->orWhere('email', 'like', "%$qWord%");
            });
        }

        $name = Arr::get($data, 'filter.name');
        if (!empty($name)) {
            $model->where('name', 'like', "%$name%");
        }

        $email = Arr::get($data, 'filter.email');
        if (!empty($email)) {
            $model->where('email', 'like', "%$email%");
        }

        return $model;
    }

    public function browse($data = null)
    {
        $model = $this->getQuery($data);

        CommonHelper::sortPageFilter($model, $data);

        $response = $model->with('users')->get();

        return $response;
    }


    public function find($uuid)
    {
        $user = User::where('uuid', $uuid)
            ->first();

        return $user;
    }

    public function findMyProfile($userUUID, $roleConstantValue)
    {
        if (Role::ROLE_MEDICAL_DOCTOR === $roleConstantValue) {
            $profile = MedicalPerson::where('user_uuid', $userUUID)->first();
            return $profile;
        }

        if (Role::ROLE_NURSE === $roleConstantValue) {
            $profile = Nurse::where('user_uuid', $userUUID)->first();
            return $profile;
        }

        if (Role::ROLE_CUSTOMER === $roleConstantValue) {
            $profile = Customer::where('user_uuid', $userUUID)->first();
            return $profile;
        }

        return null;
    }

    public function findByEmail($email)
    {
        $user = User::where('email', $email)
            ->first();

        return $user;
    }

    public function count($data)
    {
        $model = $this->getQuery($data);
        return $model->count();
    }

    public function register($user) {
        try {
            DB::transaction(function () use($user) {
                $user = User::create([
                    'name' => Arr::get($user, 'name'),
                    'email' => Arr::get($user, 'email'),
                    'password' => Hash::make(Arr::get($user, 'password')),
                ]);

                $role = Role::where('constant_value', Role::ROLE_CUSTOMER)->firstOrFail();

                UserRole::create([
                    'user_uuid' => $user->uuid,
                    'role_uuid' => $role->uuid,
                    'constant_value' => $role->constant_value,
                    'is_active' => 1,
                    'is_confirmed' => 1,
                ]);
            });

            return $this->findByEmail(Arr::get($user, 'email'));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function registerByAdmin($data) {
        try {
            DB::transaction(function () use($data) {
                $user = User::create([
                    'name' => Arr::get($data, 'name'),
                    'email' => Arr::get($data, 'email'),
                    'password' => Hash::make(Arr::get($data, 'password')),
                ]);

                $role = Role::where('uuid', Arr::get($data, 'role_uuid'))->firstOrFail();

                UserRole::create([
                    'user_uuid' => $user->uuid,
                    'role_uuid' => $role->uuid,
                    'constant_value' => $role->constant_value,
                    'is_active' => 1,
                    'is_confirmed' => 1,
                ]);
            });

            return $this->findByEmail(Arr::get($data, 'email'));
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
