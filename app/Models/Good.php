<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Good extends Model
{
    // 软删除
    use SoftDeletes;

    // 接受的字段
    protected $fillable = [
        'title', 'price', 'vipprice', 'classify', 'desc', 'detail', 'stock', 'parameter', 'clicks', 'buys',
    ];

    // 表格隐藏的字段
    protected $hidden = [
        'likes'
        // 'updated_at'
    ];
}
