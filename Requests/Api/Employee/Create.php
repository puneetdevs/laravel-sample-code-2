<?php

namespace App\Http\Requests\Api\Employee;

use Dingo\Api\Http\FormRequest;
use Auth;

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
        $this->region = Auth::user()->region_id;
        return [
            'emp_code' => 'required|exists:employee_counter_prefix,Emp_Cntr_Code,region_id,'.$this->region.',deleted_at,NULL',
            'FirstName' => ['required', 'max:20'],
            'LastName' => ['required', 'max:40'],
            'Email' => ['required','max:60', 'unique:peoples,Email,NULL,PeopleID,deleted_at,NULL'],
            'AddressLine1' => ['max:100'],
            'AddressLine2' => ['max:100'],
            'City' => ['max:20'],
            'Prov' => ['max:2'],
            'Postal' => ['max:11'],
            'Country' => ['max:25'],
            'Region' => ['max:20'],
            'Company' => ['max:30'],
            '[Union]' => ['max:15'],
            'Home' => ['max:15'],
            '[Work]' => ['max:15'],
            'WorkExt' => 'required|numeric|digits_between:1,5',
            'Cell' => 'required|numeric|digits_between:8,15',
            'Pager' => ['max:15'],
            'Fax' => ['max:15'],
            'Notes' => ['max:255'],
            'DateOfHire' => 'nullable|date_format:Y-m-d',
            'MailingList' => ['max:2'],
            'DateOfBirth' => 'nullable|date_format:Y-m-d',
            'DateCreated' =>  'nullable|date_format:Y-m-d',
            'DateLastUpdated' =>'nullable|date_format:Y-m-d',
            //'EmployeeNumber' =>['required','max:20', 'unique:peoples,EmployeeNumber,NULL,ID,deleted_at,NULL'],
            'EmergencyContact' => ['max:50'],
            'EmergencyPhone' => ['max:10'],
            'SpecialCondition' => ['max:255'],
            'ShirtSize' => ['max:15'],
            'Rating' => ['max:2'],
            'PhotoFile' => ['max:255'],
            'MobileInfo' => ['max:60'],
            'Sex' => ['max:1'],
            'Suite' => ['max:15'],
            'CallFlag' => ['max:1'],
            'NickName' => ['max:20'],
            'do_not_call' => ['in:0,1'],
            'do_not_call_reason' => ['max:250']
          //  'EvalEffectiveDate' => ['NULL','date_format:Y-m-d']
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
            'FirstName.required'   => 'First Name is required',
            'LastName.required'   => 'Last Name is required',
            'Email.unique'   => 'Employee Email is already exists, Please enter new email'
        ];
    }

}
