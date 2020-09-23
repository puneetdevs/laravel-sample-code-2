<?php

namespace App\Http\Requests\Api\Tblevents;

use Dingo\Api\Http\FormRequest;
use Auth;

class QucikRequest extends FormRequest
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
        $this->region = Auth::user()->region_id;
        return [
            'employee_id' => 'required|exists:peoples,PeopleID,region_id,'.$this->region.',deleted_at,NULL',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'event_id' => 'nullable|exists:events,EID,region_id,'.$this->region.',deleted_at,NULL',
            'position_id' => 'nullable|exists:positions,PID,region_id,'.$this->region.',deleted_at,NULL',
            'client_id' => 'nullable|exists:clients,ID,region_id,'.$this->region.',deleted_at,NULL',
            'work_category_id' => 'nullable|exists:work_categories,ID,region_id,'.$this->region.',deleted_at,NULL',
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
