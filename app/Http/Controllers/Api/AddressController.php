<?php

namespace App\Http\Controllers\Api;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    // 添加地址
    public function add(Request $request){
        // 新增地址时判断是否设置默认
        if ($request->active) {
            $this->removeActive($request->user_id);
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

        if ($oldAddress) {
            $oldAddress->update([$oldAddress->active = null]);
        }
    }

    // 设置默认地址
    public function setActive(Request $request) {
        $this->removeActive($request->user_id);
        
        $address = Address::findOrFail($request->id);
        $address->update([$address->active = 'active']);

        return $this->message('默认地址设置成功！');
    }

    // 修改地址
    public function edit(Request $request){
        if ($request->active) {
            $this->removeActive($request->user_id);
        }
        $address = Address::findOrFail($request->id);
        $address->update($request->all());

        return $this->message('地址修改成功！');
    }

    // 删除地址
    public function delete(Request $request){
        Address::findOrFail($request->id)->delete();
        return $this->message('地址删除成功');
    }

    //获取用户地址列表
    public function list(Request $request){
        $addressList = Address::where('user_id', $request->user_id)->get();
        return $this->success($addressList);
    }
}
