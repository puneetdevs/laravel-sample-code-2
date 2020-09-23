<?php

namespace App\Http\Requests\Api\ChatGroup;

use Dingo\Api\Http\FormRequest;
use App\User;

class MessageSeen extends FormRequest 
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
			'group_uuid' => 'required|max:255|exists:chat_group,group_uuid,deleted_at,NULL',
			'type' => 'required|numeric|in:1,2',
			'people_id' => 'required|numeric'
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
