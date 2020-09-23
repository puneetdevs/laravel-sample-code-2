<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;
use OwenIt\Auditing\Contracts\Auditable;
/**
   @property varchar $name name
@property date $date date
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property timestamp $deleted_at deleted at
   
 */

class HolidayList extends Model implements Auditable
{
    use SoftDeletes;
    use BelongsToTenants;
    use \OwenIt\Auditing\Auditable;
    /**
    * Database table name
    */
    protected $table = 'stat_holidays';
    public $tenantColumns = ['region_id'];

    /**
    * Mass assignable columns
    */
    protected $fillable=['Description', 'StatDate','region_id'];

    /**
    * Date time columns.
    */
    protected $dates=['StatDate', 'created_at', 'updated_at', 'deteted_at'];
    
    protected $primaryKey = 'ID';

}