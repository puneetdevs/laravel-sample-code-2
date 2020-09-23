<?php

namespace App\Http\Requests\Api\File;

use Illuminate\Foundation\Http\FormRequest;

class DocumentUpload extends FormRequest
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
            'file' => 'mimes:pdf,jpg,jpeg,docx,doc,xls,xlsx,ppt,pptx,doc|required|max:100000',
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
            'file.mimes'   => 'File format must be pdf, jpg, docx, doc, xls, xlsx, ppt, pptx, doc only',
            'file.required'   => 'File format is required',
            'file.max'   => 'File not exeed mode than 10MB'
        ];
    }
}
