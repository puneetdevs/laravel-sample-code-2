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
class Department extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    use BelongsToTenants;
    public $tenantColumns = ['region_id'];
    /**
    * Database table name
    */
    protected $table = 'departments';

    /**
    * Mass assignable columns
    */
    protected $fillable=['Departments','status','region_id'];

    /**
    * Date time columns.
    */
    protected $dates=[];
    protected $primaryKey = 'ID';



}