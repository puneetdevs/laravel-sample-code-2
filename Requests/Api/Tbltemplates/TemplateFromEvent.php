<?php

namespace App\Http\Requests\Api\Tbltemplates;

use Dingo\Api\Http\FormRequest;

class TemplateFromEvent extends FormRequest 
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
            'EID' => 'required|numeric|exists:events,EID,deleted_at,NULL',
			'TemplateName' => 'required|max:50|unique:templates,TemplateName'
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
     
        ];
    }

}
