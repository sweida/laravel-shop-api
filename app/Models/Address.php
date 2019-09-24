<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    // 接受的字段
    protected $fillable = [
        'name', 'phone', 'address', 'city', 'active', 'user_id'
    ];

    // 表格隐藏的字段
    protected $hidden = [
        'updated_at', 
    ];
}
