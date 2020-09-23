<?php

namespace App\Http\Requests\Api\Configurations;

use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rule;

class ErrorRequest extends FormRequest 
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
            'ExcessInvoiceRate_Flag' => 'required',
            'ExcessPayrollRate_Flag' => 'required',
            'ExcessPay_Flag' => 'required',
            'ShortHours_Flag' => 'required',
            'ShortInvoiceRate_Flag' => 'required',
            'ExcessFlatRate_Invoice' => 'required',
            'ExcessFlatRate_Payroll' => 'required'
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
