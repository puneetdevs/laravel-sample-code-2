<?php

namespace App\Http\Requests\Api\Tblevents;

use Dingo\Api\Http\FormRequest;

class Tbleventdaterequest extends FormRequest 
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
			'Eventdate' => 'required|date',
			'EventDescription' => 'required|max:50',
			'StatHoliday' => 'boolean',
			'DataChecked' => 'boolean',
			'SortOrder' => 'numeric',
			'DoubleTime' => 'boolean',
			'Invoiced' => 'boolean',
			'DBO' => 'boolean',
			'Tax1' => 'boolean',
			'Tax2' => 'boolean',
			'ApplySurcharge' => 'boolean'
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
