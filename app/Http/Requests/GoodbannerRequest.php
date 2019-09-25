<?php

namespace App\Http\Requests;


class GoodbannerRequest extends FormRequest
{
    public function rules()
    {
        if (FormRequest::getPathInfo() == '/api/v1/good/addbanner'){
            return [
                'good_id' => ['required', 'exists:goods,id'],
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
            'good_id.required'=>'商品id不能为空',
            'good_id.exists' => '商品不存在',
            // 'good_id.required' => '商品id不能为空',
            // 'good_id.exists' => '商品不存在',
        ];
    }
}
