<?php

namespace App\Http\Requests\Api\Region\EmployeeCounter;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class EMupdate extends FormRequest
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
        $this->region_id = Auth::user()->region_id;
        return [
            'ID' => 'required|exists:employee_counter_prefix,ID,deleted_at,NULL',
            'Emp_Cntr_Code' => 'required|max:6|unique:employee_counter_prefix,Emp_Cntr_Code,'.$this->ID.',id,deleted_at,NULL,region_id,'.$this->region_id,
            'Emp_Cntr_Description' => 'required|max:250',
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
