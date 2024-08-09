<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Nurse extends Model
{
    use HasUuids;

    protected $table = "nurses";

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
        'specialization',
    ];
}
