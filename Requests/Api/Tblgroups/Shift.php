<?php

namespace App\Http\Requests\Api\Tblgroups;

use Dingo\Api\Http\FormRequest;

class Shift extends FormRequest 
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
            'ID' => 'required|exists:events_shift_hours,ID',
            'PeopleID' => 'nullable|exists:peoples,PeopleID',
            'EID' => 'required|exists:events,EID',
            'PID' => 'required|exists:positions,PID',
            'DID' => 'required|exists:events_event_dates,DID',
            'schedule_id' => 'required|exists:event_schedules,id',
            'quick_decline' => 'required|boolean'
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
