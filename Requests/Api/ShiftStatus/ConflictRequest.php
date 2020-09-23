<?php

namespace App\Http\Requests\Api\ShiftStatus;

use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rule;
class ConflictRequest extends FormRequest 
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
            'event_id' => 'required|numeric|exists:events,EID,deleted_at,NULL',
            'start_date' => 'required|date_format:Y-m-d',
            'number_of_week' => 'required|numeric',
            'employee_id.*' => 'required|numeric|exists:peoples,PeopleID,deleted_at,NULL',
            'more_then_hours' => 'required',
            'show_warning_hours' => 'required'
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
