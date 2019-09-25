<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    // 接受的字段
    protected $fillable = [
        'user_id', 'good_id'
    ];

    // 表格隐藏的字段
    protected $hidden = [
        'updated_at', 
    ];
}
