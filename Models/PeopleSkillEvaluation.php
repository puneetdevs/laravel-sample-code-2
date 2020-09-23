<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PeopleSkillEvaluation extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'people_skill_evaluation';
    protected $fillable= ['PeopleID', 'SkillEvaluationID', 'Evaluation'];

    protected $primaryKey = 'ID';
    
    /**
    * Date time columns.
    */

    protected $dates=['created_at'];

    public function CourseDetails(){
        return $this->hasOne(Skill::class, 'SkID', 'SkillEvaluationID');
    }
}
