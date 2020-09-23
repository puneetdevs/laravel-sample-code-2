<?php

namespace App\Http\Requests\Api\ConfigurationTemplates;

use Dingo\Api\Http\FormRequest;
use Auth;
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
        $region_id = Auth::user()->region_id;
        return [
			'ConfigName' => 'required|max:30|unique:configuration_templates,ConfigName,NULL,ID,region_id,'.$region_id.',deleted_at,NULL',
		       
            'BreakPeriod' => 'nullable|max:10',
            'OverTimeCalc' => 'nullable|integer',
            'OTStarts' => 'nullable|integer',
            'DTStarts' => 'nullable|integer',
            'BreakPeriod' => 'nullable|integer',
            'BreakPeriodAcross' => 'nullable|numeric|between:0,99.99',
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
