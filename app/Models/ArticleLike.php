<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleLike extends Model
{
    // 接受的字段
    protected $fillable = [
        'article_id', 'user_id'
    ];

    protected $hidden = [
        'id'
    ];

    // 数据填充时自动忽略这个字段
    public $timestamps = false;

}
