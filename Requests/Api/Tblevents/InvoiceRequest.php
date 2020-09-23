<?php

namespace App\Http\Requests\Api\Tblevents;

use Dingo\Api\Http\FormRequest;

class InvoiceRequest extends FormRequest 
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
            'DID' => 'required',
            'DID.*' => 'required|numeric|exists:events_event_dates,DID,deleted_at,NULL',
            'schedule' => 'required|numeric|between:1,2',
            'print_as_estimate' => 'required|boolean',
            'invoiced' => 'required|boolean',
            'detail_summary' => 'required|boolean',
            'close_report' => 'nullable|boolean'
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
