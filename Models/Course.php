<?php
namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;

use Illuminate\Database\Eloquent\Model;
/**
   @property varchar $name name
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property timestamp $deleted_at deleted at
   
 */
class Course extends Model 
{
    use SoftDeletes;
    use BelongsToTenants;
    public $tenantColumns = ['region_id'];
    /**
    * Database table name
    */
    protected $table = 'courses';

    /**
    * Mass assignable columns
    */
    protected $fillable=['name','region_id'];

    /**
    * Date time columns.
    */
    protected $dates=[];




}