<?php

namespace App\Http\Requests\Api\Employee;

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
            'FirstName' => [ 'max:20'],
            'LastName' => [ 'max:40'],
            'AddressLine1' => ['max:100'],
            'AddressLine2' => ['max:100'],
            'Email' => ['required','max:60', 'unique:peoples,Email,'. $this->id.',PeopleID,deleted_at,NULL'],
            'City' => ['max:20'],
            'Prov' => ['max:2'],
            'Postal' => ['max:11'],
            'Country' => ['max:25'],
            'Region' => ['max:20'],
            'Company' => ['max:30'],
            '[Union]' => ['max:15'],
            'Home' => ['max:15'],
            '[Work]' => ['max:15'],
            'WorkExt' => 'nullable|numeric|digits_between:1,5',
            'Cell' => 'nullable|numeric|digits_between:8,15',
            'Pager' => ['max:15'],
            'Fax' => ['max:15'],
            'Notes' => ['max:255'],
            'DateOfHire' => 'nullable|date_format:Y-m-d',
            'MailingList' => ['max:2'],
            'DateOfBirth' => 'nullable|date_format:Y-m-d',
            'DateCreated' =>  'nullable|date_format:Y-m-d',
            'DateLastUpdated' =>'nullable|date_format:Y-m-d',
            'EmergencyContact' => ['max:50'],
            'EmergencyPhone' => ['max:10'],
            'SpecialCondition' => ['max:255'],
            'ShirtSize' => ['max:15'],
            'Rating' => ['max:2'],
            'PhotoFile' => ['max:255'],
            'MobileInfo' => ['max:60'],
            'Sex' => ['max:1'],
            'SIN' => 'nullable|unique:peoples,SIN,'. $this->id.',PeopleID,deleted_at,NULL',
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
            'SIN.unique'   => 'The SIN has already been taken',
            'Email.unique'   => 'Employee Email is already exists, Please enter new email'
        ];
    }

}
