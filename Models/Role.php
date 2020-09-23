<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
/**
   @property varchar $name name
@property varchar $description description
@property varchar $slug slug
@property datetime $created_at created at
@property datetime $updated_at updated at
   
 */
class Role extends Model implements Auditable 
{
    use \OwenIt\Auditing\Auditable;
    /**
    * Database table name
    */
    protected $table = 'roles';

    /**
    * Mass assignable columns
    */
    protected $fillable=['name',
'description',
'slug'];

    /**
    * Date time columns.
    */
    protected $dates=[];




}