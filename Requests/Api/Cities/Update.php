<?php

namespace App\Http\Requests\Api\Cities;

use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rule;
use Auth;

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

        $this->region_id = Auth::user()->region_id;
        
        return [
            'name' => [
                'required',
                'unique:city,City,' . $this->id.',ID,deleted_at,NULL,region_id,'.$this->region_id
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
            'name.required'   => 'City Name is required',
            'name.unique'   => 'City Name should be unique',
        ];
    }

}
