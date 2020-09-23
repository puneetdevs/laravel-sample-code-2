<?php

namespace App\Http\Requests\Api\Tblworkcategory;

use Dingo\Api\Http\FormRequest;
use Auth;

class Store extends FormRequest 
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
        $region_id = Auth::user()->region_id;
        return [
			'Description' => 'required|max:255',
			'Code' => 'required|max:10',
			'SortOrder' => 'required|unique:work_categories,SortOrder,'.$region_id.',region_id|numeric',
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
