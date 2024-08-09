<?php

namespace App\Repositories;

use App\Helpers\CommonHelper;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleUserRepository
{

    function __construct() {
    }

    private function getQuery($data = null)
    {
        $model = UserRole::join('users', 'users_roles.user_uuid', '=', 'users.uuid')
            ->select(
                'users.name as user_name',
                'users.email as email'
            );

        $qWord = Arr::get($data, 'q');
        if (!empty($qWord)) {
            $model->where(function ($query) use ($qWord) {
                $query->where('users.name', 'like', "%$qWord%");
                $query->orWhere('users.email', 'like', "%$qWord%");
            });
        }

        $uuid = Arr::get($data, 'filter.uuid');
        if (!empty($name)) {
            $model->where('uuid', '=', $uuid);
        }

        $roleUuid = Arr::get($data, 'filter.role_uuid');
        if (!empty($roleUuid)) {
            $model->where('role_uuid', '=', $roleUuid);
        }

        $userUuid = Arr::get($data, 'filter.user_uuid');
        if (!empty($userUuid)) {
            $model->where('user_uuid', '=', $userUuid);
        }

        return $model;
    }

    public function browse($data = null)
    {
        $model = $this->getQuery($data);

        CommonHelper::sortPageFilter($model, $data);

        $response = $model->get();
        $response->map(function($model){
            if ($model->is_active == 1) {
                $model->is_active_label = 'active';
                $model->is_active_label_color = 'info';
            } else {
                $model->is_active_label = 'not active';
                $model->is_active_label_color = 'warning';
            }

            if ($model->is_confirmed == 1) {
                $model->is_confirmed_label = 'confirmed';
                $model->is_confirmed_label_color = 'info';
            } else {
                $model->is_confirmed_label = 'not confirmed';
                $model->is_confirmed_label_color = 'warning';
            }


        });

        return $response;
    }

    public function find($uuid)
    {
        $role = UserRole::where('uuid', $uuid)
            ->first();

        return $role;
    }

    public function findByUserUUID($userUUID)
    {
        $role = UserRole::join('roles', 'users_roles.role_uuid', '=', 'roles.uuid')
            ->where('users_roles.user_uuid', $userUUID)
            ->select(
                'roles.uuid as uuid',
                'roles.name as name',
                'roles.constant_value as constant_value',
                'roles.created_at as created_at',
                'roles.updated_at as updated_at',
            )->first();

        return $role;
    }

    public function add($data)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => Arr::get($data, 'user_name'),
                'email' => Arr::get($data, 'email'),
                'password' => Hash::make(Arr::get($data, 'password')),
            ]);

            $model = new UserRole();
            $model->user_uuid = $user->uuid;
            $model->role_uuid = Arr::get($data, 'role_uuid');
            $model->is_active = Arr::get($data, 'is_active');
            $model->is_confirmed = Arr::get($data, 'is_confirmed');
            $model->save();

            DB::commit();

            return $model->find($model->uuid);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($uuid, $data)
    {
        $userRole = UserRole::where('user_uuid', $uuid)->update([
                'role_uuid' => Arr::get($data, 'role_uuid'),
                'is_active' => Arr::get($data, 'is_active'),
                'constant_value' => Arr::get($data, 'constant_value'),
                'is_confirmed' => Arr::get($data, 'is_confirmed'),
                'updated_at' => Carbon::now(),
            ]);

        return $userRole;
    }

    public function delete(UserRole $roleUser)
    {
        return $roleUser->delete();
    }

    public function count($data)
    {
        $model = $this->getQuery($data);
        return $model->count();
    }

    public static function isSuperAdmin($userUuid)
    {
        $role = UserRole::where('user_uuid', $userUuid)
            ->where('constant_value', 1)
            ->count();

        return $role > 0;
    }

    public static function isAllowedChangeRoleAdmin($userRole)
    {
        $role = UserRole::where('uuid', '!=', $userRole->uuid)
            ->where('constant_value', Role::ROLE_ADMIN)
            ->count();

        return $role === 0;
    }

    public function isActiveRole($userUuid)
    {
        $hasActiveRole = UserRole::where('user_uuid', $userUuid)
            ->where('is_active', 1)
            ->count();

        return $hasActiveRole > 0;
    }

    public function assign($data)
    {
        try {
            $model = new UserRole();
            $model->user_uuid = Arr::get($data, 'user_uuid');
            $model->role_uuid = Arr::get($data, 'role_uuid');
            $model->constant_value = Arr::get($data, 'constant_value');
            $model->is_active = 1;
            $model->is_confirmed = 1;
            $model->save();

            return $model->find($model->uuid);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function isAlreadyAssigned($data)
    {
        $role = UserRole::where('user_uuid', Arr::get($data, 'user_uuid'))
            ->where('role_uuid', Arr::get($data, 'role_uuid'))
            ->count();

        return $role > 0;
    }

    public function browseOptions($data = null)
    {
        $model = $this->getQuery($data);

        CommonHelper::sortPageFilter($model, $data);

        $userUUIDList = $model->pluck('user_uuid')->toArray();

        $response = User::select('uuid', 'name', 'email')->whereIn('uuid', $userUUIDList)->orderBy('email', 'asc')->get();

        return $response;
    }
}
