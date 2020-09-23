<?php

namespace App\Http\Requests\Api\BulkMessageTemplate;

use Dingo\Api\Http\FormRequest;

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
            'id' => 'required|numeric|exists:bulk_message_template,id',
			'title' => 'required|max:255|unique:bulk_message_template,title,'.$this->id.'id',
			'type' => 'required|max:100',
			'subject' => 'nullable|max:255',
			'message' => 'required|string',
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
