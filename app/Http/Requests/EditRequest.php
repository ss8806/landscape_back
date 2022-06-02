<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class EditRequest extends FormRequest
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
            'title' => ['min:5|max:20'],
            'body' => ['min:5|max:200'],
            // 'pic1' => ['mimes:jpg,jpeg,png'],
        ];
    }

    public function attributes()
    {
        return [
            'title'  => 'タイトル',
            'body'  => '本文',
            'pic1'  => '写真',
        ];
    }
}
