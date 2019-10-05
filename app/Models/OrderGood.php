<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderGood extends Model
{
    // 接受的字段
    protected $fillable = [
        'order_id',
        'good_id',
        'good_name',
        'label',
        'price',
        'count'
    ];

    // 数据填充时自动忽略这个字段
    public $timestamps = false;

}
