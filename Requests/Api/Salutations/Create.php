<?php

namespace App\Http\Requests\Api\Salutations;

use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rule;
use Auth;

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
        $region_id = Auth::user()->region_id;
        return [
            'name' => [
                'required',
                'unique:salutation,Option,NULL,id,region_id,'.$region_id.',deleted_at,NULL'
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
            'name.required'   => 'Salutation Name is required',
            'name.unique'   => 'Salutation Name must be Unique this name is already exists.',
        ];
    }

}
