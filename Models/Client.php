<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;
use OwenIt\Auditing\Contracts\Auditable;
/**
   @property varchar $name name
@property varchar $abbrevation abbrevation
@property varchar $adddress adddress
@property varchar $city city
@property varchar $providance providance
@property varchar $postal_code postal code
@property varchar $country country
@property varchar $email email
@property varchar $phone phone
@property text $notes notes
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property timestamp $deleted_at deleted at
   
 */
class Client extends Model implements Auditable
{
    use BelongsToTenants, SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    public $tenantColumns = ['region_id'];
    /**
    * Database table name
    */
    protected $table = 'clients';

    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'region_id',
        'Name',
        'AddressLine1',
        'AddressLine2',
        'City',
        'abbrevation',
        'Prov',
        'Postal',
        'Country',
        'email',
        'ClientPhone',
        'ClientFax',
        'GSTNumber',
        'ClientNotes',
        'PhoneExt',
        'status'
    ];

    /**
    * Date time columns.
    */
    protected $dates=[];
    protected $primaryKey = 'ID';



}