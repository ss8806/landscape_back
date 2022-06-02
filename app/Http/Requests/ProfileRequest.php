<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'editIcon'  => 'require|file|image',
            'editName'  => 'min:2|max:12',
            'editEmail' => 'min:1|max:30|email',
            'password' => 'confirmed|min:4|max:8',
            // 'password' => ['confirmed', Rules\Password::defaults()],
        ];
    }
    public function attributes()
    {
        return [
            'editIcon'  => 'アイコン',
            'editName'  => '名前',
            'editEmail'  => 'メールアドレス',
            'password'  => 'パスワード',
        ];
    }
}
