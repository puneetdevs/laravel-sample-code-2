<?php

namespace App\Http\Requests\Api\EventShift;

use Dingo\Api\Http\FormRequest;

class UpdateShift extends FormRequest 
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
			'shift' => 'required',
            'shift.*.EID' => 'required|numeric|exists:events,EID,deleted_at,NULL',
            'shift.*.DID' => 'required|numeric|exists:events_event_dates,DID,deleted_at,NULL',
            'shift.*.PeopleID' => 'required|numeric|exists:peoples,PeopleID,deleted_at,NULL',
            'shift.*.shift_id' => 'required|numeric|exists:events_shift_hours,ID,deleted_at,NULL',
            'shift.*.shift_id' => 'required|numeric|exists:events_shift_hours,ID,deleted_at,NULL',
            'shift.*.EID' => 'required|numeric|exists:events_shift_hours,EID,deleted_at,NULL',
            'shift.*.DID' => 'required|numeric|exists:events_shift_hours,DID,deleted_at,NULL',
            'shift.*.PeopleID' => 'required|numeric|exists:events_shift_hours,PeopleID,deleted_at,NULL',
            'shift.*.Start1' => 'required|date_format:H:i',
            'shift.*.Finish1' => 'required|date_format:H:i',
            'shift.*.Start2' => 'nullable|date_format:H:i',
            'shift.*.Finish2' => 'nullable|date_format:H:i',
            'shift.*.Start3' => 'nullable|date_format:H:i',
            'shift.*.Finish3' => 'nullable|date_format:H:i',
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
