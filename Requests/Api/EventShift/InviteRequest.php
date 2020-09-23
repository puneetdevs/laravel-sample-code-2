<?php

namespace App\Http\Requests\Api\EventShift;

use Dingo\Api\Http\FormRequest;

class InviteRequest extends FormRequest 
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
			'data' => 'required',
            'data.*.EID' => 'required|numeric|exists:events,EID,deleted_at,NULL',
            'data.*.date_data' => 'required',
            'data.*.date_data.*.DID' => 'required|numeric|exists:events_event_dates,DID,deleted_at,NULL',
            'data.*.date_data.*.is_publish' => 'required|boolean',
            'data.*.date_data.*.PID' => 'required|numeric|exists:positions,PID,deleted_at,NULL',
            'data.*.date_data.*.shift_id' => 'required|numeric|exists:event_schedules,id,deleted_at,NULL',
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
        return [
     
        ];
    }

}
