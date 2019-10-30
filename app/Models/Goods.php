<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goods extends Model
{
    // 软删除
    use SoftDeletes;

    // 该模型数据库允许插入的字段，其它模型插入的字段可以不用管，controller是可以接收的到的
    protected $fillable = [
        'title', 'classify', 'desc', 'detail', 'parameter', 'clicks', 'buys',
    ];

    // 表格隐藏的字段
    protected $hidden = [
        // 'updated_at'
    ];
}
