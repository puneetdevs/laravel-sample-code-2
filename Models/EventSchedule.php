<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Position;
use App\Models\Tbleventdate;

class EventSchedule extends Model implements Auditable 
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    /**
    * Database table name
    */
    protected $table = 'event_schedules';

    /**
    * Mass assignable columns
    */
    protected $fillable=[
        
        'event_id',
        'position_id',
        'date_id',
        'quantity',
        'department_id',
        'start_one',
        'finish_one',
        'start_two',
        'finish_two',
        'start_three',
        'finish_three',
        ];
    
    #Remove seconds form start and finish time    
    public function getStartOneAttribute($value)
    {
        return !is_null($value) ? date('H:i', strtotime($value)) : '';
    }
    public function getfinishOneAttribute($value)
    {
        return !is_null($value) ? date('H:i', strtotime($value)) : '';
    }
    public function getStartTwoAttribute($value)
    {
        return !is_null($value) ? date('H:i', strtotime($value)) : '';
    }
    public function getFinishTwoAttribute($value)
    {
        return !is_null($value) ? date('H:i', strtotime($value)) : '';
    }
    public function getStartThreeAttribute($value)
    {
        return !is_null($value) ? date('H:i', strtotime($value)) : '';
    }
    public function getFinishThreeAttribute($value)
    {
        return !is_null($value) ? date('H:i', strtotime($value)) : '';
    }
        
    //Position relation with event_schedule
    public function position(){
        return $this->hasOne(Position::class,'PID','position_id');
    }  

    //Event relation with event_schedule
    public function event(){
        return $this->hasOne(Tblevent::class,'EID','event_id');
    }

    //Department relation with event_schedule
    public function department(){
        return $this->hasOne(Department::class,'ID','department_id');
    }

    //Date relation with event_schedule
    public function date(){
        return $this->hasOne(Tbleventdate::class,'DID','date_id');
    } 

    //Shidt relation with event_schedule
    public function shift(){
        return $this->hasMany(TbleventsShifthour::class,'schedule_id','id');
    } 

    //Shidt relation with event_schedule
    public function shifts(){
        return $this->hasMany(TbleventsShifthour::class,'position_id','PID');
    }

    
    
}