<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderGoods extends Model
{
    // 接受的字段
    protected $fillable = [
        'order_id',
        'goods_id',
        'goods_name',
        'label',
        'label_id',
        'price',
        'count'
    ];

    // 数据填充时自动忽略这个字段
    public $timestamps = false;
}


