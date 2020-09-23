<?php

namespace App\Http\Requests\Api\EmployeeApplications;

use Dingo\Api\Http\FormRequest;
use Auth;

class AcceptReject extends FormRequest 
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
        $this->region = Auth::user()->region_id;
        return [
			'Status' => 'required',
            'application_id' => 'required|exists:employee_applications,id',
            'emp_code' => 'nullable|exists:employee_counter_prefix,Emp_Cntr_Code,region_id,'.$this->region.',deleted_at,NULL',
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
