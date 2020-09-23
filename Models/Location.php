<?php
namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use HipsterJazzbo\Landlord\BelongsToTenants;
use OwenIt\Auditing\Contracts\Auditable;
/**
   @property varchar $name name
@property varchar $phone phone
@property varchar $email email
@property varchar $fax fax
@property varchar $address_1 address 1
@property varchar $address_2 address 2
@property varchar $city city
@property varchar $providence providence
@property varchar $postal_code postal code
@property varchar $country country
@property int $default_configuration_id default configuration id
@property int $schedue schedue
@property varchar $vanue_code vanue code
@property varchar $directions directions
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property timestamp $deleted_at deleted at
   
 */
class Location extends Model implements Auditable
{
    use SoftDeletes;
    use BelongsToTenants;
    use \OwenIt\Auditing\Auditable;
    /**
    * Database table name
    */
    protected $table = 'locations';
    public $tenantColumns = ['region_id'];
    protected $primaryKey = 'VID';
    //public $incrementing = true;

    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'CfgID',
        'VName',
        'VenueEmail',
        'VenueFax',
        'VAddressLine1',
        'VAddressLine2',
        'VCity',
        'VProv',
        'VPostal',
        'VCountry',
        'CfgID',
        'DefaultRateSchedule',
        'VenueCode',
        'VDirections',
        'VenuePhone',
        'VLat',
        'VLng',
        'Website',
        'status'
    ];

    /**
    * Date time columns.
    */
    protected $dates=['created_at'];

    


}