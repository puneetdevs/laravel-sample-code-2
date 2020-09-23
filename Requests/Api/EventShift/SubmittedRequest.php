<?php

namespace App\Http\Requests\Api\EventShift;

use Dingo\Api\Http\FormRequest;

class SubmittedRequest extends FormRequest 
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
			'DID' => 'required|numeric|exists:events_event_dates,DID,deleted_at,NULL',
            'DID' => 'required|numeric|exists:shift_approval,DID,deleted_at,NULL'
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
