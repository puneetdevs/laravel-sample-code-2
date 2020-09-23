<?php

namespace App\Http\Requests\Api\Tblevents;

use Dingo\Api\Http\FormRequest;

class Schedulerequest extends FormRequest 
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
            'data.*' => 'required',
            'data.*.DID' => 'required|numeric|exists:events_event_dates,DID,EID,'.$this->id,
            'data.*.schedule' => 'required',
            'data.*.schedule.*.id' => 'nullable|numeric|exists:event_schedules,id,event_id,'.$this->id,
			'data.*.schedule.*.quantity' => 'required|numeric',
			'data.*.schedule.*.start_one' => 'required|date_format:H:i',
			'data.*.schedule.*.finish_one' => 'required|date_format:H:i',
			'data.*.schedule.*.start_two' => 'nullable|date_format:H:i',
			'data.*.schedule.*.finish_two' => 'nullable|date_format:H:i',
			'data.*.schedule.*.start_three' => 'nullable|date_format:H:i',
			'data.*.schedule.*.finish_three' => 'nullable|date_format:H:i',
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
            '*.position_id.required' => 'A Position is required'
           
        ];
    }

}
