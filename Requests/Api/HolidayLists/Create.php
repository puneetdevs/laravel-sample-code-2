<?php

namespace App\Http\Requests\Api\HolidayLists;

use Dingo\Api\Http\FormRequest;

class Create extends FormRequest 
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
            'name' => ['required', 'max:35'],
            'date' => ['required','date_format:Y-m-d','after:start_date']
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
            'name.required'   => 'Holiday Name is required',
            'date.after'   => 'Holiday date must be start from today date.',
            'date.date_format'   => 'Holiday date format must be YYYY-MM-DD'
        ];
    }

}
