<?php

namespace App\Http\Requests\Api\Region;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
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
            'Tax1_Rate' => ['numeric','between:0,99.99','nullable'],
            'Tax1_RegNum' => ['integer','nullable'],
            'Tax1_ApplyLabour' => ['in:0,1', 'nullable'],
            'Tax2_Rate' => ['numeric','between:0,99.99', 'nullable'],
            'Tax2_RegNum' => ['integer','nullable'],
            'Tax2_ApplyLabour' => ['in:0,1', 'nullable'],
            'Tax3_Rate' => ['numeric','between:0,99.99', 'nullable'],
            'Tax3_RegNum' => ['integer','nullable'],
            'Tax3_ApplyLabour' => ['in:0,1', 'nullable'],
            'Default_AS_Tax1' => ['in:0,1', 'nullable'],
            'Default_AS_Tax2' => ['in:0,1', 'nullable'],
            'Default_AS_Tax3' => ['in:0,1', 'nullable'],
            'WeeklyOTHours' => ['numeric','between:0,999.99', 'nullable'],
            'Province' => ['max:2', 'nullable'],
            'Phone' => ['max:10', 'nullable'],
            'Fax' => ['max:10', 'nullable'],
            'date' => ['date_format:Y-m-d','after:start_date', 'nullable'],
            'FirstDayofYear' => ['date_format:Y-m-d', 'nullable'],
            'RegionCode' =>  ['max:2', 'nullable'],
            'Gst' =>  ['max:15', 'nullable'],
            'EmployeeCounter' =>  ['numeric', 'nullable'],
            'EventCounter' =>  ['numeric', 'nullable']
        ];
    }
}
