<?php

namespace App\Http\Controllers\Api;


use App\Models\User;
use Hash;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

// use App\Http\Resources\UserResource;

class UserController extends Controller
{
    // 授权登录
    public function onLogin(Request $request) {
        $code = $request->get('code');
        $encryptedData = $request->get('encryptedData');
        $iv = $request->get('iv');
        $appid = env('WX_OPENID');
        $secret = env('WX_SECRET_KEY');

        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', 'https://api.weixin.qq.com/sns/jscode2session', [
            'query' => [
                'appid' =>$appid,
                'secret' => $secret,
                'js_code' => $code,
                'grant_type' => 'authorization_code'
            ]
        ]);
        $body = json_decode($res->getBody());

        if (property_exists($body, 'errcode')) {
            return $this->failed('登录授权失败！请重新授权', 200);
        }

        $openid = $body->openid;
        $session_key = $body->session_key;
        
        $userifo = new \WXBizDataCrypt($appid, $session_key);
        
        $errCode = $userifo->decryptData($encryptedData, $iv, $data);
        $info = json_decode($data);  

        $filterName = $this->filter($info->nickName);

        User::updateOrCreate(
            ['openid' => $openid],
            $user = [
                'openid' => $openid,
                'session_key' => $session_key,
                'nickName' => $filterName,
                'avatarUrl' => $info->avatarUrl,
                'province' => $info->province,
                'city' => $info->city
            ]
        );
        return $this->success($user);
    }

    // 去掉昵称特殊字符
    public function filter($str) {
        if($str){
            $name = $str;
            $name = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $name);
            $name = preg_replace('/xE0[x80-x9F][x80-xBF]‘.‘|xED[xA0-xBF][x80-xBF]/S','?', $name);
            $return = json_decode(preg_replace("#(\\\ud[0-9a-f]{3})#","",json_encode($name)));
        }else{
            $return = '';
        }
        return $return;
    }

    //用户注册
    public function signup(UserRequest $request){
        User::create($request->all());
        return $this->message('用户注册成功');
    }

    //用户登录
    public function login(UserRequest $request){
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
    
    //用户退出
    public function logout(){
        Auth::guard('api')->logout();
        return $this->message('退出登录成功!');
    }

    //返回当前登录用户信息
    public function info(){
        $user = Auth::guard('api')->user();
        if ($user->is_admin==1)
            $user->admin = true;
        return $this->success($user);
    }

    //返回指定用户信息
    public function show(UserRequest $request){
        $user = User::find($request->id);
        return $this->success($user);
    }

    //返回用户列表 10个用户为一页
    public function list(){
        $users = User::paginate(10);
        foreach($users as $item) {
            if ($item->is_admin) {
                $item->admin = true;
            }
        }
        // return UserResource::collection($users);
        return $this->success($users);
    }

    // 修改密码
    public function resetpassword(UserRequest $request){
        $user = Auth::guard('api')->user();
        $oldpassword = $request->get('old_password');

        if (!Hash::check($oldpassword, $user->password))
            return $this->failed('旧密码错误', 200);

        $user->update(['password' => $request->new_password]);
        return $this->message('密码修改成功');
    }


}
