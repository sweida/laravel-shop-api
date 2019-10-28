<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model
{

    use SoftDeletes;
    // 接受的字段
    protected $fillable = [
        'name', 'phone', 'email', 'info', 'avatarUrl'
    ];

    // 表格隐藏的字段
    protected $hidden = [
        
    ];

}