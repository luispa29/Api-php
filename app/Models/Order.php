<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
        use SoftDeletes;

       protected $fillable = [
        'name',
        'status',
        'description',
        'price',
        'weight',
        'customer_id',
    ];
}
