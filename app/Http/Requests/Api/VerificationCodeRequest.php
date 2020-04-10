<?php

namespace App\Http\Requests\Api;


class VerificationCodeRequest extends FormRequest
{

    public function rules()
    {
        return [
            'captcha_code' => 'required|string',
            'captcha_key' => 'required|string'
        ];
    }

    public function attributes()
    {
        return [
            'captcha_code' => '验证码',
            'captcha_key' => '验证码 key'
        ];
    }
}
