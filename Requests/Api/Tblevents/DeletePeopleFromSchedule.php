<?php

namespace App\Http\Requests\Api\Tblevents;

use Dingo\Api\Http\FormRequest;

class DeletePeopleFromSchedule extends FormRequest 
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
            'shift.*' => 'required|numeric|exists:events_shift_hours,ID'
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
