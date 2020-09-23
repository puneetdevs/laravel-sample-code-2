<?php

namespace App\Http\Requests\Api\EmailTemplates;

use Dingo\Api\Http\FormRequest;

class Update extends FormRequest 
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
            'name' => [
                'required',
                'unique:email_templates,name,' . $this->id.',id,deleted_at,NULL'
            ],
            'slug' => [
                'required',
                'unique:email_templates,slug,' . $this->id.',id,deleted_at,NULL'
            ],
            'content' => [
                'required'
            ],
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
            'name.required'   => 'Email Templates Name is required',
            'name.unique'   => 'Email Templates Name is alreay used please choose any other name.',
            'slug.unique'   => 'Email Templates slug is alreay used please choose any other name.',
            'slug.required'   => 'Email Templates slug is required',
            'content.required'   => 'Email Templates content is required'
        ];
    }

}
