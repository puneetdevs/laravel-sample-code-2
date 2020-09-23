<?php

namespace App\Http\Requests\Api\BulkMessageTemplate;

use Dingo\Api\Http\FormRequest;

class SendBulk extends FormRequest 
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
            'email_template_id' => 'nullable|numeric|exists:bulk_message_template,id',
			'sms_template_id' => 'nullable|numeric|exists:bulk_message_template,id',
            'email' => 'nullable|boolean',
            'sms' => 'nullable|boolean',
			'EID' => 'required|numeric|exists:events,EID',
            'date_data.*' => 'required',
            'date_data.*.DID' => 'required|numeric|exists:events_event_dates,DID,EID,'.$this->EID,
            'date_data.*.PID.*' => 'required|numeric|exists:positions,PID'
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
