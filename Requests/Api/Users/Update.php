<?php

namespace App\Http\Requests\Api\Users;

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
			'selected_team' => 'nullable|numeric',
			'added_by' => 'nullable|numeric',
			'phonenumber' => 'nullable|numeric',
			//'email' => 'required|email|unique:users',
			'img' => 'nullable|numeric|exists:files,id',
			'FirstName' => 'nullable|max:20',
			'LastName' => 'nullable|max:40',
			'AddressLine1' => 'nullable|max:50',
			'Suite' => 'nullable|max:15',
			'Prov' => 'nullable|max:2',
			'City' => 'nullable|max:20',
			'Country' => 'nullable|max:25',
			'Postal' => 'nullable|max:10',
			'role_id' => 'required|numeric|exists:roles,id'
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
