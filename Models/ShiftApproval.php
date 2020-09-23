<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\TbleventsShifthour;
/**
   @property int $shift_id shift id
@property int $EID EID
@property int $DID DID
@property int $PeopleID PeopleID
@property int $PID PID
@property time $Start1 Start1
@property time $Finish1 Finish1
@property time $Start2 Start2
@property time $Finish2 Finish2
@property time $Start3 Start3
@property time $Finish3 Finish3
@property int $Status Status
@property tinyint $is_view is view
@property int $region_id region id
@property datetime $created_at created at
@property datetime $updated_at updated at
@property datetime $deleted_at deleted at
   
 */
class ShiftApproval extends Model implements Auditable
{
    use SoftDeletes;
    use BelongsToTenants;
    use \OwenIt\Auditing\Auditable;
    /**
    * Database table name
    */
    protected $table = 'shift_approval';

    public $tenantColumns = ['region_id'];

    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'shift_id',
        'EID',
        'DID',
        'PeopleID',
        'PID',
        'Start1',
        'Finish1',
        'Start2',
        'Finish2',
        'Start3',
        'Finish3',
        'Status',
        'is_view',
        'Department'
    ];

    #Remove seconds form start and finish time    
    public function getStart1Attribute($value)
    {
        return !is_null($value) ? date('H:i', strtotime($value)) : '';
    }
    public function getfinish1Attribute($value)
    {
        return !is_null($value) ? date('H:i', strtotime($value)) : '';
    }
    public function getStart2Attribute($value)
    {
        return !is_null($value) ? date('H:i', strtotime($value)) : '';
    }
    public function getFinish2Attribute($value)
    {
        return !is_null($value) ? date('H:i', strtotime($value)) : '';
    }
    public function getStart3Attribute($value)
    {
        return !is_null($value) ? date('H:i', strtotime($value)) : '';
    }
    public function getFinish3Attribute($value)
    {
        return !is_null($value) ? date('H:i', strtotime($value)) : '';
    }

    //Position relation with event_schedule
    public function position(){
        return $this->hasOne(Position::class,'PID','PID');
    }  

    //People relation with event_schedule
    public function people(){
        return $this->hasOne(People::class,'PeopleID','PeopleID');
    }
    
    //Event relation with people_notifications
    public function event(){
        return $this->hasOne(Tblevent::class,'EID','EID');
    }

    //Date relation with people_notifications
    public function date(){
        return $this->hasOne(Tbleventdate::class,'DID','DID');
    }

    //Department relation with event_schedule
    public function department(){
        return $this->hasOne(Department::class,'ID','Department');
    }
    
    //Shift relation with event_schedule
    public function shift(){
        return $this->hasOne(TbleventsShifthour::class,'ID','shift_id');
    }
    

}