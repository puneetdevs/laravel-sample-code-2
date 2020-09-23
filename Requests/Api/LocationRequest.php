<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class LocationRequest extends FormRequest 
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
			'name' => 'required|max:100',
			'phone' => 'required|max:20',
			'email' => 'nullable|max:80',
			'fax' => 'nullable|max:20',
			'address_1' => 'required|max:255',
			'address_2' => 'nullable|max:255',
			'city' => 'required|max:50',
			'providence' => 'required|max:50',
			'postal_code' => 'required|max:20',
			'country' => 'required|max:50',
			'default_configuration_id' => 'required|numeric',
			'schedue' => 'required|numeric',
			'vanue_code' => 'required|max:50',
			'directions' => 'nullable|max:255',
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
