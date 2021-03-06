<?php

namespace App\Http\Requests\Api\Configurations;

use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rule;

class Create extends FormRequest 
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
            'ConfigName' => 'required|max:100',
            'BreakPeriod' => 'nullable|max:10',
            'OverTimeCalc' => 'nullable|integer',
            'OTStarts' => 'nullable|integer',
            'DTStarts' => 'nullable|integer',
            'BreakPeriod' => 'nullable|integer',
            'BreakPeriodAcross' => 'nullable|numeric|between:0,99.99',
            'ConfigTemplateID' => 'nullable|numeric|exists:configuration_templates,ID,deleted_at,NULL'
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
            'name.required'   => 'City Name is required',
            'name.unique'   => 'City Name should be unique'
        ];
    }

}
