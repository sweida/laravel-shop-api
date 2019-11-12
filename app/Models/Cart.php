<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    // 接受的字段
    protected $fillable = [
        'goods_id', 'count', 'label_id', 'user_id'
    ];

    // 表格隐藏的字段
    protected $hidden = [
        'updated_at'
    ];
}
