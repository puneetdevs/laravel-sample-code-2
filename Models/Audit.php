<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use HipsterJazzbo\Landlord\BelongsToTenants;
use Carbon\Carbon;
use App\Models\DataFileSettings;
use Auth;
/**
   @property varchar $user_type user type
@property bigint $user_id user id
@property varchar $event event
@property varchar $auditable_type auditable type
@property bigint $auditable_id auditable id
@property text $old_values old values
@property text $new_values new values
@property text $url url
@property varchar $ip_address ip address
@property varchar $user_agent user agent
@property varchar $tags tags
@property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class Audit extends Model 
{
    /**
    * Database table name
    */
    use BelongsToTenants;
    protected $table = 'audits';
    public $tenantColumns = ['region_id'];
    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'user_type',
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
        'region_id',
        'tags'
    ];

    public function getUserTypeAttribute($value)
    {
        return 'User';
    }
    // public function getAuditableTypeAttribute($value)
    // {
    //     $value1 = str_replace('App','',$value);
    //     $value2 = str_replace('\Models','',$value1);
    //     return substr($value2, 1);
    // }
    // public function getOldValuesAttribute($value)
    // {
    //     // $value1 = str_replace('{"','',$value);
    //     // $value2 = str_replace('}','',$value1);
    //     // $value3 = str_replace('"','',$value2);
    //     // $value4 = explode(',',$value3);
    //     return $value;
    // }
    // public function getNewValuesAttribute($value)
    // {
    //     // $value1 = str_replace('{"','',$value);
    //     // $value2 = str_replace('}','',$value1);
    //     // $value3 = str_replace('"','',$value2);
    //     // $value4 = explode(',',$value3);
    //     return $value;
    // }

    public function getCreatedAtAttribute($value){
        $date = $value;
        $audit = self::first();
        if($audit){
            $date_setting = DataFileSettings::where('region_id', $audit->region_id)->first();
            if($date_setting && isset($date_setting->time_zone) && !empty($date_setting->time_zone)){
                $date = Carbon::createFromFormat('Y-m-d H:i:s', $value, 'UTC');
                $date->setTimezone($date_setting->time_zone);
            }
        }
        if($date){
            $date = Carbon::parse($date)->toIso8601String();
        }
        return $date;
    }

    public function getUpdatedAtAttribute($value){
        $date = $value;
        
        $audit = self::first();
        if($audit){
            $date_setting = DataFileSettings::where('region_id', $audit->region_id)->first();
            if($date_setting && isset($date_setting->time_zone) && !empty($date_setting->time_zone)){
                $date = Carbon::createFromFormat('Y-m-d H:i:s', $value, 'UTC');
                $date->setTimezone($date_setting->time_zone);
            }
        }
        if($date){
            $date = Carbon::parse($date)->toIso8601String();
        }
        return $date;
    }

    /**
    * Date time columns.
    */
    protected $dates=[];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }


}