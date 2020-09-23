<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HipsterJazzbo\Landlord\BelongsToTenants;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
   @property varchar $FirstName FirstName
@property varchar $LastName LastName
@property varchar $Initial Initial
@property varchar $Address1 Address1
@property varchar $Address2 Address2
@property varchar $Suite Suite
@property varchar $Country Country
@property varchar $Email Email
@property varchar $Province Province
@property varchar $City City
@property varchar $Postal_code Postal code
@property varchar $Country_code Country code
@property varchar $Cell Cell
@property date $DateOfBirth DateOfBirth
@property int $Status Status
@property text $rejected_reason rejected reason
@property int $EmployeeID EmployeeID
@property datetime $created_at created at
@property datetime $updated_at updated at
@property datetime $deleted_at deleted at
   
 */
class EmployeeApplication extends Model 
{
    use SoftDeletes;
    use BelongsToTenants;
    /**
    * Database table name
    */
    protected $table = 'employee_applications';

    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'FirstName',
        'LastName',
        'Initial',
        'Address1',
        'Address2',
        'Suite',
        'Country',
        'Email',
        'Province',
        'City',
        'Postal_code',
        'Country_code',
        'Cell',
        'DateOfBirth',
        'Status',
        'rejected_reason',
        'EmployeeID',
        'ProfileImage',
        'region_id',
        'action_date'
    ];

    /**
    * Date time columns.
    */
    protected $dates=['DateOfBirth'];

    // protected $appends = ['timezone_completed_at'];

    // #Get Region Timezone set in created_at field
    // public function getTimezoneCompletedAtAttribute()
    // {
    //     $created_at = $this->created_at;
    //     if(!empty($this->created_at))
    //     {
    //         $timezone = \App\Models\DataFileSettings::select('time_zone')->where('region_id',\Auth::user()->region_id)->first();
    //         if($timezone && $timezone->time_zone){
    //             $created_at =  \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$created_at)->timezone($timezone->time_zone)->format('Y-m-d H:i:s');
    //         }
    //     }
    //     return $created_at;        
    // }

}