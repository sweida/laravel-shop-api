<?php

namespace App\Http\Controllers\Api;


use App\Models\User;
use App\Models\Address;
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
        $appid = env('WX_APPID');
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

        // 默认地址
        $address = Address::where([ 'user_id'=>$openid, 'active'=>'active' ])->first();
        // 收藏夹数量
        $likeCount = (new CollectionController)->likesGoodsCount($openid);

        // 解密失败时，判断用户是否存在，
        // 1、存在的，解密失败时获取旧的数据即可
        // 2、不存在的要重新授权
        if ($errCode!='0') {
            // 还没注册的
            $isOldUser = User::where('openid', $openid)->first();
            
            if (!$isOldUser) {
                return $this->failed('获取用户信息失败！', 200);
            } else {
                $isOldUser->defaultAddress = $address;
                $isOldUser->likeCount = $likeCount;
                return $this->success($isOldUser);
            }
        }

        $user = [
            'openid' => $openid,
            'session_key' => $session_key,
            'nickName' => $info->nickName,
            'avatarUrl' => $info->avatarUrl,
            'province' => $info->province,
            'city' => $info->city,
            'defaultAddress' => $address,
            'likeCount' => $likeCount
        ];

        User::updateOrCreate( ['openid' => $openid], $user);
        return $this->success($user);
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
