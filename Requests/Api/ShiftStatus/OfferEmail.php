<?php

namespace App\Http\Requests\Api\ShiftStatus;

use Dingo\Api\Http\FormRequest;

class OfferEmail extends FormRequest 
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
            'EID' => 'required|numeric|exists:events,EID,deleted_at,NULL',
            'DID' => 'required|numeric|exists:events_event_dates,DID,deleted_at,NULL,EID,'.$this->EID
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
