<?php

namespace App\Http\Requests\Api\ClientContacts;

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
			'client_id' => 'required|numeric',
			'name' => 'required|max:100',
			'title' => 'required|max:100',
			'email' => 'nullable|max:50',
			'home_phone' => 'nullable|max:20',
			'work_phone' => 'nullable|max:20',
			'cell_phone' => 'nullable|max:20',
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
