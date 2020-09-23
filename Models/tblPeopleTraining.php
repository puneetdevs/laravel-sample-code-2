<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class tblPeopleTraining extends Model implements Auditable 
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    protected $table = "people_training";

    protected $fillable = [
        'people_id',
        'course_id',
        'completed',
        'completed_date',
        'expire_date',
        'certificate_number',
        'file_id'
    ];
    public function FileDetails(){
        return $this->hasOne(File::class, 'id', 'file_id');
    }
    public function CourseDetails(){
        return $this->hasOne(Course::class, 'id', 'course_id');
    }
    public function people(){
        return $this->hasOne(People::class, 'PeopleID', 'people_id');
    }
    
}
