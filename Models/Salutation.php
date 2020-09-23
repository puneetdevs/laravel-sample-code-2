<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;
/**
   @property varchar $name name
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property timestamp $deleted_at deleted at
   
 */
class Salutation extends Model 
{
    use SoftDeletes;
    use BelongsToTenants;

    /**
    * Database table name
    */
    protected $table = 'salutation';
    public $tenantColumns = ['region_id'];

    /**
    * Mass assignable columns
    */
    protected $fillable=['Option','region_id'];

    /**
    * Date time columns.
    */
    protected $dates=['create_at', 'deleted_at', 'updated_at'];

    protected $primaryKey = 'id';


}