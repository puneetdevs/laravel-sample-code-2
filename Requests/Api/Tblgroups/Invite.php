<?php

namespace App\Http\Requests\Api\Tblgroups;

use Dingo\Api\Http\FormRequest;

class Invite extends FormRequest 
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
            'people_obj.*.people_id' => 'required|exists:peoples,PeopleID,deleted_at,NULL',
            'EID' => 'required|exists:events,EID,deleted_at,NULL',
            'PID' => 'required|exists:positions,PID,deleted_at,NULL',
            'schedule_id.*' => 'required|exists:event_schedules,id,deleted_at,NULL',
            'DID.*' => 'required|exists:events_event_dates,DID,deleted_at,NULL'
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
