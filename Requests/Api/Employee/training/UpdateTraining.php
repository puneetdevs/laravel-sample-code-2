<?php

namespace App\Http\Requests\Api\Employee\training;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTraining extends FormRequest
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
            'course_id' => ['required'],
            'completed' => ['required', Rule::in([0, 1])],
            'completed_date' =>['date_format:Y-m-d'],
            'expire_date' =>['date_format:Y-m-d']
        ];
    }
}
