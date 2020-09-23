<?php

namespace App\Http\Requests\Api\TbleventsInvoicelineitems;

use Dingo\Api\Http\FormRequest;

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
        return [
			'EID' => 'required|numeric|exists:events,EID',
			'Description' => 'required|max:255',
			'Amount' => 'required|numeric',
            'Tax1' => 'required|boolean',
            'Tax2' => 'required|boolean',
            'Tax3' => 'required|boolean'
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
