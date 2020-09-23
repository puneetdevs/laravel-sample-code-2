<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Input;
use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
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
        $rules = [];

        if (Input::has('email')) {
            $rules = [
                'email' => 'required|email',
            ];
        } elseif (Input::has('password_reset_token')) {
            $rules = [
                'new_password' => 'required',
                'confirm_password' => 'required|same:new_password',
                'password_reset_token' => 'required',
            ];
        } else {
            $rules = [
                'old_password' => 'required',
                'new_password' => 'required',
                'confirm_password' => 'required|same:new_password'
            ];
        }
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'confirm_password.same' => 'New Password and confirm password must be same',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
            'data' => [],
        ], 422);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
