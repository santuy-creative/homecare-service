<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasUuids, HasFactory;

    protected $table = "users_roles";

    protected $primaryKey = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_uuid',
        'role_uuid',
        'constant_value',
        'is_active',
        'is_confirmed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
