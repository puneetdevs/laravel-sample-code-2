<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HipsterJazzbo\Landlord\BelongsToTenants;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use App\User;
use DB;
use App\Models\PeopleSkillEvaluation;
use App\Models\PeopleNotAvailabilities;
use App\Models\TbleventsShifthour;

class People extends Model implements Auditable
{
    use BelongsToTenants, SoftDeletes;
    use \OwenIt\Auditing\Auditable;
   
    protected $table = 'peoples';
    
    public $tenantColumns = ['region_id'];

    protected $fillable=['Lat', 'Lng', 'Salutation', 'FirstName', 'Initial', 'LastName', 'AddressLine1', 'EmployeeNumber', 'AddressLine2', 'City', 'Prov', 'Postal', 'Country', 'Region', 'Company','[Union]','Home','[Work]','WorkExt', 'Cell', 'Pager', 'Fax', 'Email', 'Notes', 'DateOfHire', 'MailingList', 'DateOfBirth', 'DateCreated', 'DateLastUpdated', 'UpdatedBy', 'SIN', 'ContactType', 'EmployeeNumber', 'EmergencyContact', 'EmergencyPhone', 'EmergencyExt', 'SpecialCondition', 'ShirtSize', 'Rating', 'Filter', 'Availability', 'AvailableAnytime', 'PhotoFile', 'MobileInfo', 'Sex', 'Suite', 'CallFlag', 'NickName', 'EvalEffectiveDate', 'YearsNotWorked', 'TotalHiringFactor', 'user_id', 'region_id', 'do_not_call', 'do_not_call_reason', 'is_archive'];

    protected $primaryKey = 'PeopleID';
     /**
    * Date time columns.
    */
    protected $dates=['created_at'];

    //Employee User relation
    public function user() {
        return $this->hasOne(User::class,'id','user_id');
    }

    //Skill relation with people
    public function skill(){
        return $this->hasMany(PeopleSkillEvaluation::class,'PeopleID','PeopleID');
    }

    //people_not_available relation with people
    public function people_notavailable(){
        return $this->hasMany(PeopleNotAvailabilities::class,'peopleId','PeopleID');
    }

    //shift relation with People
    public function shift(){
        return $this->hasMany(TbleventsShifthour::class,'PeopleID','PeopleID');
    }

}
