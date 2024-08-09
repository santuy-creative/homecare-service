<?php

namespace Database\Seeders;

use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dateNow = Carbon::now();
        $roles = [
            [
                'uuid' => Str::uuid(),
                'name' => 'Super Admin',
                'constant_value' => 1,
                'created_at' => $dateNow,
                'updated_at' => $dateNow,
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Admin',
                'constant_value' => 2,
                'created_at' => $dateNow,
                'updated_at' => $dateNow,
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Medical Doctor',
                'constant_value' => 3,
                'created_at' => $dateNow,
                'updated_at' => $dateNow,
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Nurse',
                'constant_value' => 4,
                'created_at' => $dateNow,
                'updated_at' => $dateNow,
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Customer',
                'constant_value' => 5,
                'created_at' => $dateNow,
                'updated_at' => $dateNow,
            ],
        ];

        foreach($roles as $role) {
            \App\Models\Role::factory()->create($role);
            $roleConstantValue = Arr::get($role, 'constant_value');
            if ($roleConstantValue === 1) {
                $userData = [
                    'uuid' => Str::uuid(),
                    'name' => 'alimasyhur',
                    'email' => 'jegrag4ever@gmail.com',
                    'password' => Hash::make('homecare-123'),
                    'created_at' => $dateNow,
                    'updated_at' => $dateNow,
                ];
                \App\Models\User::factory()->create($userData);

                $userRoleData = [
                    'uuid' => Str::uuid(),
                    'user_uuid' => Arr::get($userData, 'uuid'),
                    'role_uuid' => Arr::get($role, 'uuid'),
                    'constant_value' => Arr::get($role, 'constant_value'),
                    'is_active' => 1,
                    'is_confirmed' => 1,
                    'created_at' => $dateNow,
                    'updated_at' => $dateNow,
                ];
                \App\Models\UserRole::factory()->create($userRoleData);
            }
        }
    }
}
