<?php

namespace App\Http\Requests\Api\Employee\Evaluations;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluation extends FormRequest
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
            'EvaluationDate' => ['date_format:Y-m-d'],
            'YearsNotWorked' => ['integer','max:10'],
            'PerformanceFactor' => ['numeric','between:0,99.99'],
            'LoyaltyFactor' => ['numeric','between:0,99.99'],
            'SubTotalHiringFactor' => ['numeric','between:0,99.99'],
            'TotalHiringFactor' => ['numeric','between:0,99.99'],
            'IndustryExperience' => ['integer','max:10'],
            'Punctuality' => ['integer','max:10'],
            'AttentionToSafety' => ['integer','max:10'],
            'AttentionToDetails' => ['integer','max:10'],
            'ConductAndAttitude' => ['integer','max:10'],
            'Preparedness' => ['integer','max:10'],
            'TeamWorker' => ['integer','max:10'],
            'EmployeeRelations' => ['integer','max:10'],
            'ClientRelations' => ['integer','max:10']
        ];
    }
}
