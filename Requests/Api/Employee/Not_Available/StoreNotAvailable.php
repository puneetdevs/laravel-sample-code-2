<?php

namespace App\Http\Requests\Api\Employee\Not_Available;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreNotAvailable extends FormRequest
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
            'StartDate' => ['required', 'date_format:Y-m-d'],
            'EndDate' => ['required', 'date_format:Y-m-d','after_or_equal:StartDate'],
            'Reason' => ['required', 'max:100']
        ];
    }
}
