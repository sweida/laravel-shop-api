<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * @OA\Post(
     *     path="/admin/signup",
     *     tags={"管理员"},
     *     summary="管理员注册",
     *     description="管理员注册",
     *     deprecated=false,
     *     @OA\Parameter(
     *         name="access_token",
     *         in="query",
     *         description="用户授权",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="Accept",
     *         description="Accept header to specify api version",
     *         required=false,
     *         in="header",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         description="The page num of the list",
     *         required=false,
     *         in="query",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         description="The item num per page",
     *         required=false,
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="管理员注册成功!"
     *     ),
     * )
     */
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
        $Admins = Admin::withTrashed()->paginate(10);
        return $this->success($Admins);
    }

    // 禁用or启动
    public function deleteOrRestored(Request $request){
        $admin = Admin::withTrashed()->findOrFail($request->id);
        if ($admin->deleted_at) {
            $admin->restore();
        } else {
            $admin->delete();
        }
        return $this->message('操作成功！');
    }

    // 禁用
    public function delete(Request $request){
        Admin::findOrFail($request->id)->delete();
        return $this->message('管理员已禁用');
    }

    // 启用
    public function restored(Request $request){
        Admin::withTrashed()->findOrFail($request->id)->restore();
        return $this->message('管理员已启用');
    }

    // 真删除
    public function reallyDelete(Request $request){
        Admin::withTrashed()->findOrFail($request->id)->forceDelete();
        return $this->message('管理员删除成功');
    }


}
