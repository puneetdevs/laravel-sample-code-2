<?php

namespace App\Http\Requests\Api\Configurations\RateConfig;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRateConfig extends FormRequest
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
            'PID' => ['required'],
            'BasePay' => ['nullable', 'numeric','between:0,999.99'],
            'BaseCharge' => ['nullable','numeric','between:0,999.99'],
            'OtherCharge' => ['nullable','numeric','between:0,999.99'],
            'Flat' => ['required', 'in:0,1'],
            'EnablePayrollAdj' => ['required', 'in:0,1'],
            'Country' => ['max:25'],
            'Prov' => ['max:2']
        ];
    }
}
