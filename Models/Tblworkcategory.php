<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;
/**
   @property varchar $Description Description
@property varchar $Code Code
@property int $SortOrder SortOrder
   
 */
class Tblworkcategory extends Model implements Auditable 
{
    use \OwenIt\Auditing\Auditable;
    /**
    * Database table name
    */
    protected $table = 'work_categories';

    use SoftDeletes;
    use BelongsToTenants;
    
    public $tenantColumns = ['region_id'];

    /**
    * Mass assignable columns
    */
    protected $fillable=['SortOrder',
'Description',
'Code',
'SortOrder',
'region_id'];

    /**
    * Date time columns.
    */
    protected $dates=[];

    protected $primaryKey = 'ID';




}