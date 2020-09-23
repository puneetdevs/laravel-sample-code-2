<?php

namespace App\Http\Requests\Api\ShiftStatus;

use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rule;
class Create extends FormRequest 
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
            'name' => [
                'required',
                'unique:shift_status,Status,NULL,id,deleted_at,NULL'
            ],
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
            'name.required'   => 'Status Name is required',
            'name.unique'   => 'Status Name should be unique',
        ];
    }

}
