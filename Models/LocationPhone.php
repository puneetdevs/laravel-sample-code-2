<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
   @property int $location_id location id
@property varchar $description description
@property varchar $phone phone
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property timestamp $deleted_at deleted at
   
 */
class LocationPhone extends Model 
{
    use SoftDeletes;
    /**
    * Database table name
    */
    protected $table = 'locations_contacts';
    protected $primaryKey = 'ID';
    /**
    * Mass assignable columns
    */
    protected $fillable=['VID', 'PhoneText', 'PhoneNumber', 'Email', 'Name', 'Position'];

    /**
    * Date time columns.
    */
    protected $dates=[];




}