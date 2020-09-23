<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class LocationPhoneRequest extends FormRequest 
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
			'location_id' => 'required|numeric',
			'description' => 'required|max:100',
			'phone' => 'required|max:20',
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
