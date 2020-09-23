<?php

namespace App\Http\Requests\Api\Employee\Skill;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSkill extends FormRequest
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
            'SkillEvaluationID' => ['required'],
            'Evaluation' => ['required','integer','max:10']
        ];
    }
}
