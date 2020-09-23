<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;
use OwenIt\Auditing\Contracts\Auditable;
/**
   @property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class Position extends Model implements Auditable
{
    use SoftDeletes;
    use BelongsToTenants;
    use \OwenIt\Auditing\Auditable;
    
    /**
    * Database table name
    */
    protected $table = 'positions';

    public $tenantColumns = ['region_id'];

    /**
    * Mass assignable columns
    */
    protected $fillable=['SortOrder', 'Position', 'Filter', 'status', 'region_id'];

    /**
    * Date time columns.
    */
    protected $dates=['created_at', 'updated_at'];

    protected $primaryKey = 'PID';



}