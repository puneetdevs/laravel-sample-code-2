<?php

namespace App\Http\Requests\Api\LocationPhones;

use Dingo\Api\Http\FormRequest;

class UpdatePhone extends FormRequest 
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
			'description' => 'max:100',
            'phone' => 'numeric|required',
            'email' => 'email'
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
