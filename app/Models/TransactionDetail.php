<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionDetail extends Model
{
    use HasUuids, HasFactory;

    protected $table = "transaction_details";

    protected $primaryKey = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_uuid',
        'name',
        'status',
        'quantity',
        'unit_price',
        'sub_total',
        'item_type',
        'item_uuid',
    ];
}
