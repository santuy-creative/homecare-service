<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasUuids, HasFactory;

    protected $table = "roles";

    protected $primaryKey = 'uuid';

    const ROLE_SUPER_ADMIN = 1;
    const ROLE_ADMIN = 2;
    const ROLE_MEDICAL_DOCTOR = 3;
    const ROLE_NURSE = 4;
    const ROLE_CUSTOMER = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'constant_value',
    ];
}
