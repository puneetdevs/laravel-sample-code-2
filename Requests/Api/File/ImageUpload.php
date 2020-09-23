<?php

namespace App\Http\Requests\Api\File;

use Illuminate\Foundation\Http\FormRequest;

class ImageUpload extends FormRequest
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
            'image' => 'mimes:jpeg,jpg,png,gif|required|max:100000',
        ];
    }

    
    /**
    * Get the error messages for the defined validation rules.
    *
    * @return array
    */

    public function messages()
    {
        return [
            'image.mimes'   => 'File format must be jpeg, jpg, png, gif only',
            'image.required'   => 'image is required',
            'image.max'   => 'File not exeed mode than 10MB'
        ];
    }
}
