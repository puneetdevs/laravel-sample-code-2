<?php

namespace App\Http\Requests\Api\ChatGroup;

use Dingo\Api\Http\FormRequest;
use App\User;

class SaveMember extends FormRequest 
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
			'event_id' => 'required|numeric|exists:chat_group,event_id,deleted_at,NULL',
			'people_id.*' => 'required',
			'type' => 'required|numeric',
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
