<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;
/**
@property int $EID EID
@property int $DID DID
@property int $PeopleID PeopleID
@property tinyint $status status
@property int $region_id region id
@property datetime $created_at created at
@property datetime $updated_at updated at
@property datetime $deleted_at deleted at
 */
class PreBooking extends Model 
{
    use SoftDeletes;
    use BelongsToTenants;
    /**
    * Database table name
    */
    protected $table = 'pre_bookings';
    public $tenantColumns = ['region_id'];
    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'EID',
        'DID',
        'PeopleID',
        'status',
        'region_id',
        'note'
    ];

    /**
    * Date time columns.
    */
    protected $dates=[];

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


}