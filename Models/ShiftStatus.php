<?php
namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use HipsterJazzbo\Landlord\BelongsToTenants;
/**
   @property varchar $name name
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property timestamp $deleted_at deleted at
   
 */
class ShiftStatus extends Model 
{
    use SoftDeletes;
    use BelongsToTenants;
    
    /**
    * Database table name
    */
    protected $table = 'shift_status';

    public $tenantColumns = ['region_id'];
    /**
    * Mass assignable columns
    */
    protected $fillable=['Status'];

    /**
    * Date time columns.
    */
    protected $dates=[];

    protected $primaryKey = 'ID';


}