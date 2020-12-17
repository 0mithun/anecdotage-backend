<?php

namespace App\Http\Requests\Thread;

use Illuminate\Foundation\Http\FormRequest;

class ThreadCreateRequest extends FormRequest
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
            'title'         => ['required','unique:threads,title'],
            'body'          => ['required'],
            // 'image_path'    =>  ['mimes:png,jpg,jpeg,giff,svg','max:1024']

        ];
    }
}
