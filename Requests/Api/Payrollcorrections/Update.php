<?php

namespace App\Http\Requests\Api\Payrollcorrections;

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
			'ID' => 'required|numeric|exists:payroll_corrections,ID,deleted_at,NULL',
			'PeopleID' => 'required|numeric|exists:peoples,PeopleID,deleted_at,NULL',
			'VID' => 'required|numeric|exists:locations,VID,deleted_at,NULL',
			'EID' => 'required|numeric|exists:events,EID,deleted_at,NULL',
			'PayPeriod_Start' => 'required|date',
            'PayPeriod_End' => 'required|date',
            'correction_status' => 'required|boolean',
			'Correction' => 'required|numeric',
			'Submitter' => 'required|numeric',
			'Code' => 'required|max:50',
			'Comments' => 'nullable|max:255',
			'ReasonAmount' => 'required|boolean',
			'ReasonTimesheet' => 'required|boolean',
			'ReasonPayroll' => 'required|boolean',
			'ReasonInvoice' => 'required|boolean',
			'ReasonInvoiceDate' => 'nullable|date',
			'ReasonOther' => 'nullable|max:255',
			'SubmittedDate' => 'nullable|date',
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
