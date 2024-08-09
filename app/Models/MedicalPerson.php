<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MedicalPerson extends Model
{
    use HasUuids;

    protected $table = "medical_persons";

    protected $primaryKey = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_uuid',
        'nik',
        'firstname',
        'lastname',
        'birthdate',
        'phone',
        'bio',
        'address',
        'specialization',
        'license_number',
    ];
}
