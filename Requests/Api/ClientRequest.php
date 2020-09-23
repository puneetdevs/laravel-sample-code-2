<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class ClientRequest extends FormRequest 
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
			'abbrevation' => 'required|max:50',
			'adddress' => 'required|max:255',
			'city' => 'required|max:50',
			'providance' => 'required|max:50',
			'postal_code' => 'required|max:50',
			'country' => 'required|max:50',
			'email' => 'required|max:50',
			'phone' => 'required|max:20',
			'notes' => 'required|string',
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
