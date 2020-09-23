<?php

namespace App\Http\Requests\Api\EventShift;

use Dingo\Api\Http\FormRequest;

class ShiftRequest extends FormRequest 
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
			'EID' => 'required',
            'EID.*' => 'required|numeric|exists:events,EID',
            'employee_id' => 'required|numeric|exists:peoples,PeopleID,deleted_at,NULL'
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
