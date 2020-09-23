<?php

namespace App\Http\Requests\Api\TblpeoplePayrolladjust;

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
			'ID' => 'required|numeric|exists:people_payroll_adjust,ID,deleted_at,NULL',
			'PeopleID' => 'required|numeric|exists:peoples,PeopleID,deleted_at,NULL',
			'Value' => 'required|numeric',
			'DateEffectiveStart' => 'required|date_format:Y-m-d',
			'DateEffectiveEnd' => 'required|date_format:Y-m-d','after:DateEffectiveStart',
			'payroll_variable_type' => 'required|max:3|in:DPH,PPH',
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
