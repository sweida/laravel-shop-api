<?php

namespace App\Http\Controllers\Api;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    // 添加地址
    public function add(AddressRequest $request){
        // 新增地址时判断是否设置默认
        if ($request->active) {
            $this->removeActive($request->openid);
        }

        $address = Address::create($request->all());

        return $this->message('地址新增成功');
    }

    // 将旧的默认设置为空
    public function removeActive($userId) {
        $oldAddress = Address::where([
            'user_id'=>$userId,
            'active'=>'active'
        ])->first();

        if (!$oldAddress->isEmpty()) {
            $oldAddress->active = null;
            $oldAddress->update($oldAddress);
        }
    }

    // 设置默认地址
    public function setActive(Request $request) {
        $this->removeActive($request->openid);
        
        $address = Address::findOrFail($request->id);
        $address->active = 'active';
        $address->update($address);

        return $this->message('默认地址设置成功！');
    }

    // 修改地址
    public function edit(AddressRequest $request){
        if ($request->active) {
            $this->removeActive($request->openid);
        }
        $article = Article::findOrFail($request->id);
        $article->update($request->all());

        return $this->message('地址修改成功！');
    }

    // 真删除文章
    public function delete(AddressRequest $request){
        Address::find($request->id)->delete();
        return $this->success('地址删除成功');
    }

    //获取用户地址
    public function list(Request $request){

        $addressList = Address::where('user_id', $request->openid)->get();
        return $this->success($addressList);
    }
}
