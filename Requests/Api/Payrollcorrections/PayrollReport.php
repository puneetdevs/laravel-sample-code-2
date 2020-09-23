<?php

namespace App\Http\Requests\Api\Payrollcorrections;

use Dingo\Api\Http\FormRequest;

class PayrollReport extends FormRequest 
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
            'is_employee' => 'required|in:EXCLUDE,INCLUDE,ALL',
            'is_event' => 'required|in:EXCLUDE,INCLUDE,ALL',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
			'cheque_list' => 'required|boolean',
			'payroll_check' => 'required|boolean',
			'pay_stubs' => 'required|boolean',
			'payroll' => 'required|boolean'
        ];
    }

    /**
    * Get the error messages for the defined validation rules.
    *
    * @return array
    */
    public function messages()
    {
        return [];
    }

}
