<?php

namespace App\Http\Requests\Api\Cities;

use Dingo\Api\Http\FormRequest;
use Illuminate\Validation\Rule;
use Auth;
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
        $user = Auth::user();
        return [
            'name' => [
                'required',
                'max:35',
                 Rule::unique('city','City')->where(function ($query)use($user) {
                    return $query->where('region_id', $user->region_id)->whereNull('deleted_at');
                })
            ],
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
            'name.required'   => 'City Name is required',
            'name.unique'   => 'City Name should be unique'
        ];
    }

}
