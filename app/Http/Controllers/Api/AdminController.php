<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use App\Models\AdminAuth;
use Illuminate\Http\Request;
use App\Http\Requests\AdminRequest;
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
    public function signup(AdminRequest $request){
        $user = Admin::create($request->all());
        $emailIdentifier = [
            'user_id' => $user->id,
            'identity_type' => 'email',
            'identifier' => $request->email,
            'password' => $request->password
        ];
        $nameIdentifier = [
            'user_id' => $user->id,
            'identity_type' => 'name',
            'identifier' => $request->name,
            'password' => $request->password
        ];
        AdminAuth::create($emailIdentifier);
        AdminAuth::create($nameIdentifier);
        return $this->message('管理员注册成功');
    }

    //用户登录
    public function login(AdminRequest $request){
        $type = $request->get('type');
        $token=Auth::guard('api')->attempt(
            [
                'identity_type' => $type ? $type : 'name', 
                'identifier'=>$request->name,
                'password'=>$request->password
            ]
        );
        if($token) {
            $adminAuth = Auth::guard('api')->user();
            $admin = Admin::findOrFail($adminAuth->user_id);
            $admin->update([$admin->updated_at = time()]);

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

    // 真删除
    public function reallyDelete(Request $request){
        Admin::withTrashed()->findOrFail($request->id)->forceDelete();
        AdminAuth::where('user_id', $request->id)->delete();
        return $this->message('删除管理员成功！');
    }


}
