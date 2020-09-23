<?php

namespace App\Http\Requests\Api\Tbltemplates;

use Dingo\Api\Http\FormRequest;

class CreateEvent extends FormRequest 
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
            'TemplateID' => 'required|numeric|exists:templates,TemplateID,deleted_at,NULL',
			'EventName' => 'required|max:100',
			'WorkCategoryID' => 'required|numeric|exists:work_categories,ID,deleted_at,NULL',
			'sales_manager' => 'required|numeric|exists:users,id,role_id,4,deleted_at,NULL',
            'account_manager' => 'required|numeric|exists:users,id,role_id,3,deleted_at,NULL',
            'Booked' => 'required|boolean'
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
