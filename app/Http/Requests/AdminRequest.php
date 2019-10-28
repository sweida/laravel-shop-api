<?php

namespace App\Http\Requests;

class AdminRequest extends FormRequest
{
    public function rules()
    {
        switch (FormRequest::getPathInfo()){
            case '/api/'.env('APP_VER').'/admin/signup':
                return [
                    'name' => ['required', 'max:32', 'unique:admins,name'],
                    'email' => ['required', 'unique:admins,email'],
                    'password' => ['required', 'between:6,20'],
                    'phone' => ['unique:admins,phone']
                ];
            case '/api/'.env('APP_VER').'/admin/login':
                return [
                    'name' => ['required', 'max:32', 'exists:admin_auths,identifier'],
                    'password' => ['required', 'between:6,20'],
                ];
            case '/api/'.env('APP_VER').'/admin/resetpassword':
                return [
                    'old_password' => ['required', 'between:6,20'],
                    'new_password' => ['required', 'between:6,20'],
                ];
            default:
                return [
                    'id' => ['required', 'exists:admins,id']
                ];
        }
    }


    public function messages()
    {
        return [
            'name.required'=>'用户名不能为空',
            'name.exists'=>'用户名不存在',
            'name.max' => '用户名长度不能超过32个字符',
            'name.unique' => '用户名已经存在',
            'email.required' => '邮箱不能为空',
            'email.unique' => '邮箱已经存在',
            'phone.unique' => '手机号已存在',
            'password.required' => '密码不能为空',
            'password.between' => '密码长度为6~20位之间',
            'old_password.required' => '旧密码不能为空',
            'old_password.between' => '密码长度为6~20位之间',
            'new_password.required' => '新密码不能为空',
            'new_password.between' => '密码长度为6~20位之间',
            // 'password.max' => '密码长度不能超过32个字符',
            // 'password.min' => '密码长度不能少于6个字符', 
            'id.required'=>'id必须填写',
            'id.exists' => '用户id不存在'
        ];
    }
}