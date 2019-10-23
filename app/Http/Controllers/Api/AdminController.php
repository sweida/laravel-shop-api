<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //管理员注册
    public function signup(Request $request){
        Admin::create($request->all());
        return $this->message('管理员注册成功');
    }

    // 管理员登录
    public function login(Request $request){
        $token=Auth::guard('api')->attempt(
            ['name'=>$request->name,'password'=>$request->password]
        );
        if($token) {
            $user = Auth::guard('api')->user();
            $user->updated_at = time();
            $user->update();
            return $this->success(['token' => 'Bearer ' . $token]);
        }
        return $this->failed('密码有误！', 200);
    }

    public function list(Request $request){
        $Admins = Admin::paginate(10);
        return $this->success($Admins);
    }


}
