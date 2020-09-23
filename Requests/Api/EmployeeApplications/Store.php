<?php

namespace App\Http\Requests\Api\EmployeeApplications;

use Dingo\Api\Http\FormRequest;

class Store extends FormRequest 
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
			'FirstName' => 'required|max:255',
			'LastName' => 'required|max:255',
			'Initial' => 'required|max:5',
			'Address1' => 'required|max:255',
			'Address2' => 'nullable|max:255',
			'Suite' => 'nullable|max:255',
			'Country' => 'required|max:255',
			'Email' => 'required|max:255|unique:employee_applications,Email|unique:peoples,Email',
			'Province' => 'required|max:255',
			'City' => 'required|max:255',
			'Postal_code' => 'required|max:255',
			'Country_code' => 'required|max:10',
			'Cell' => 'required|max:15',
			'DateOfBirth' => 'nullable|date',
			'rejected_reason' => 'nullable|string',
            'EmployeeID' => 'nullable|numeric',
            'ProfileImage' => 'nullable',
            'Doc_ids' => 'required',
            'Doc_ids.*' => 'required|exists:people_documents,id'
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
            'Doc_ids.required'   => 'Resume is required'
        ];
    }

}
