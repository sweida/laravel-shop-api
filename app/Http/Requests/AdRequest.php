<?php

namespace App\Http\Requests;

class AdRequest extends FormRequest
{
    public function rules()
    {
        if (FormRequest::getPathInfo() == '/api/v1/ad/add'){
            return [
                'title' => ['required'],
                'url' => ['required'],
            ];
        } else {
            return [
                'id' => ['exists:ads,id'],
                'type' => ['exists:ads,type']
            ];
        }

    }

    public function messages()
    {
        return [
            'title.required'=>'标题不能为空',
            'url.required' => '地址不能为空',
            'id.required'=>'id不能为空',
            'id.exists' => 'id不存在',
            'type.exists' => '类型不存在',
        ];
    }
}
