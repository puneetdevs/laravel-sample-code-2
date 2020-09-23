<?php

namespace App\Http\Requests\Api\Clients;

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
			'name' => 'required|max:100',
			'abbrevation' => 'max:50',
			'adddress' => 'max:70',
			'city' => 'required|max:30',
			'providance' => 'required|max:2',
			'postal_code' => 'required|max:13',
			'country' => 'required|max:25',
			'email' => 'max:50',
            'phone' => 'max:10',
            'fax' => 'max:10',
            'gst' => 'max:15',
            'code' => 'max:50',
			'notes' => 'max:250'
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
