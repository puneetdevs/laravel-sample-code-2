<?php

namespace App\Http\Requests\Api\Tbltemplates;

use Dingo\Api\Http\FormRequest;

class Store extends FormRequest 
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
			'TemplateName' => 'required|max:50|unique:templates,TemplateName',
			'RateConfiguration' => 'required|numeric|exists:configurations,CfgID',
			'Venue' => 'required|numeric|exists:locations,VID',
			'Client' => 'required|numeric|exists:clients,ID',
			'DefaultName' => 'nullable|max:50',
			'Schedule' => 'required|numeric|max:6',
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
