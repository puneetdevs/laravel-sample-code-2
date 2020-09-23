<?php

namespace App\Http\Requests\Api\Employee;

use Dingo\Api\Http\FormRequest;
use Auth;

class ArchiveRequest extends FormRequest 
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
            'employee_id' => 'required|exists:peoples,PeopleID,deleted_at,NULL',
            'is_archive' => 'required|boolean',
        ];
    }

    /**
    * Get the error messages for the defined validation rules.
    *
    * @return array
    */
    public function messages()
    {
        return [];
    }

}
