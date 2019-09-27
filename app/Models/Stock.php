<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    // 接受的字段
    protected $fillable = [
        'good_id', 'stock', 'label', 'label_id', 'price', 'vip_price'
    ];

    // 数据填充时自动忽略这个字段
    public $timestamps = false;

    // 表格隐藏的字段
    protected $hidden = [
        'updated_at'
    ];
}
