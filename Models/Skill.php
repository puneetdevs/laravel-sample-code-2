<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;
use OwenIt\Auditing\Contracts\Auditable;

/**
   @property varchar $name name
@property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class Skill extends Model implements Auditable
{
    
    /**
    * Database table name
    */
    use SoftDeletes;
    use BelongsToTenants;
    use \OwenIt\Auditing\Auditable;
    public $tenantColumns = ['region_id'];


    protected $table = 'skills';
    

    /**
    * Mass assignable columns
    */
    protected $fillable=['Skill','region_id'];

    /**
    * Date time columns.
    */
    protected $dates = ['deleted_at'];

    protected $primaryKey = 'SkID';


}