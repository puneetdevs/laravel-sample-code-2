<?php

namespace App\Http\Requests\Api\Clients;

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
			'name' => 'required|max:100',
			'abbrevation' => 'max:50',
			'adddress' => 'max:255',
			'city' => 'required|max:50',
			'providance' => 'required|max:50',
			'postal_code' => 'required|max:50',
			'country' => 'required|max:50',
			'email' => 'max:50',
			'phone' => 'max:20',
			'notes' => 'max:500'
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
            'name.required'   => 'Client Name is required',
            'city.required'   => 'Client City is required',
            'providance.required'   => 'Client Providance is required',
            'postal_code.required'   => 'Client Postal Code is required',
            'country.required'   => 'Client Country is required'
        ];
    }

}
