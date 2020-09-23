<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PeopleEvaluation extends Model
{
    use SoftDeletes;
    protected $table = 'people_evaluation';
    protected $fillable= ['PeopleID', 'IndustryExperience', 'Punctuality', 'AttentionToSafety', 'AttentionToDetails', 'ConductAndAttitude', 'Preparedness', 'TeamWorker', 'EmployeeRelations', 'ClientRelations', 'EvaluationDate', 'YearsNotWorked', 'PerformanceFactor', 'LoyaltyFactor', 'SubTotalHiringFactor', 'TotalHiringFactor', 'TotalHiringFactor'];
 
    protected $primaryKey = 'ID';
}
