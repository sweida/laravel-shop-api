<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // 接受的字段
    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'goodPrice',
        'totalPay',
        'expressType',
        'expressName',
        'expressPrice',
        'addressName',
        'addressPhone',
        'address',
        'discount',
        'discount_id'
    ];


    // 表格隐藏的字段
    protected $hidden = [
        'updated_at'
    ];
}
