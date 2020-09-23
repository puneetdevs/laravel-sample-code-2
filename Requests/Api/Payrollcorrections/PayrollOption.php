<?php

namespace App\Http\Requests\Api\Payrollcorrections;

use Dingo\Api\Http\FormRequest;

class PayrollOption extends FormRequest 
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
			'WeeklyOTHours' => 'required',
            'OFARateInvoice' => 'required',
            'OFARatePay' => 'required',
			'PayrollPeriod' => 'required|numeric',
			'FirstDayofYear' => 'required|date'
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
