<?php

namespace App\Http\Requests\Api\Positions;

use Dingo\Api\Http\FormRequest;

class PositionRequest extends FormRequest 
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
            'pos_order' => 'required|integer',
            'name' => 'required',
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
            'pos_order.required'   => 'Position Order is required',
            'name.required'   => 'Position Name is required',
            'pos_id.required'   => 'Position Id is required',
        ];
    }

}
