<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HipsterJazzbo\Landlord\BelongsToTenants;
use OwenIt\Auditing\Contracts\Auditable;

class EventsShiftLog extends Model implements Auditable 
{
    
    use BelongsToTenants;
    use \OwenIt\Auditing\Auditable;
    public $tenantColumns = ['region_id'];
    
    /**
    * Database table name
    */
    protected $table = 'events_shift_logs';

    /**
    * Mass assignable columns
    */
    protected $fillable=[
        
        'PeopleID',
        'PID',
        'DID',
        'EID',
        'ShiftID',
        'Quantity',
        'Department',
        'Start1',
        'Finish1',
        'Confirmed',
        'region_id',
        'note',
        'is_published',
        'SNS',
        'SOC',
        'OFA',
        ];

     protected $primaryKey = 'ID';
    

     //People relation with people_notifications
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

    //Position relation with people_notifications
    public function position(){
        return $this->hasOne(Position::class,'PID','PID');
    }

    //Notification relation with TbleventsShifthour
    public function shift(){
        return $this->hasOne(TbleventsShifthour::class,'ID','ShiftID');
    }
}