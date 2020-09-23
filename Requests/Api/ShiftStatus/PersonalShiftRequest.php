<?php

namespace App\Http\Requests\Api\ShiftStatus;

use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rule;
class PersonalShiftRequest extends FormRequest 
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
            'from_date' => 'required|date_format:Y-m-d',
            'to_date' => 'required|date_format:Y-m-d',
            'email_send_to.*' => 'required|email',
            'email_subject' => 'required|max:100',
            'email_message' => 'required',
            'shift_ids' => 'required',
            'shift_ids.*' => 'required|numeric|exists:events_shift_hours,ID,deleted_at,NULL',
            'employee_id' => 'required|numeric|exists:peoples,PeopleID,deleted_at,NULL'
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
