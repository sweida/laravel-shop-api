<?php

namespace App\Http\Requests;


class CartRequest extends FormRequest
{
    public function rules()
    {
        if (FormRequest::getPathInfo() == '/api/v1/cart/person'){
            return [
                'user_id' => ['required', 'exists:users,openid']
            ];
        } else {
            return [
                'user_id' => ['required', 'exists:users,openid'],
                'goods_id' => ['required', 'exists:goods,id'],
                'label_id' => ['required'],
                'count' => ['required']
            ];
        }

    }

    public function messages()
    {
        return [
            'user_id.required'=>'用户id不能为空',
            'goods_id.required' => '商品id不能为空',
            'label_id.required' => '标签id不能为空',
            'count.required' => '商品数量不能为空',
            'user_id.exists' => '用户不存在',
            'goods_id.exists' => '商品不存在',
        ];
    }
}
