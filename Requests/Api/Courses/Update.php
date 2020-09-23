<?php

namespace App\Http\Requests\Api\Courses;

use Dingo\Api\Http\FormRequest;
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
        $region_id = Auth::user()->region_id;
        return [
            'name' => [
                'required',
                'max:35',
                'unique:courses,name,'.$this->id.',id,region_id,'.$region_id.',deleted_at,NULL'
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
            'name.required'   => 'Course Name is required',
            'name.unique'   => 'Course name is alreay used please choose any other name.',
        ];
    }

}
