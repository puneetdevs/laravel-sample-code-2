<?php

namespace App\Http\Requests\Api\Tblgroups;

use Dingo\Api\Http\FormRequest;

class OfferContent extends FormRequest 
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
            'EID' => 'required|exists:events,EID',
            'DID' => 'required|exists:events_event_dates,DID',
            'email_message' => 'required',
            'email_subject' => 'required',
            'sms_message' => 'required',
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
