<?php

namespace App\Http\Requests;


class CollectionRequest extends FormRequest
{
    public function rules()
    {
        if (FormRequest::getPathInfo() == '/api/v1/user/likesGoodList'){
            return [
                'user_id' => ['required', 'exists:users,openid'],
            ];
        } else {
            return [
                'user_id' => ['required', 'exists:users,openid'],
                'good_id' => ['required', 'exists:goods,id'],
            ];
        }

    }

    public function messages()
    {
        return [
            'user_id.required'=>'用户id不能为空',
            'good_id.required' => '商品id不能为空',
            'user_id.exists' => '用户不存在',
            'good_id.exists' => '商品不存在',
        ];
    }
}
