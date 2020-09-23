<?php

namespace App\Http\Requests\Api\Tblevents;

use Dingo\Api\Http\FormRequest;

class Bulkupdate extends FormRequest 
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
            'DID' => 'required|numeric|exists:events_event_dates,DID|exists:event_schedules,date_id',
            'EID' => 'required|numeric|exists:events,EID|exists:event_schedules,event_id',
            'schedule.*.id' => 'required|numeric|exists:event_schedules,id',
            'schedule.*.position_id' => 'required|numeric|exists:positions,PID'            
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
