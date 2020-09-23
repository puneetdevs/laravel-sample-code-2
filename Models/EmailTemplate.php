<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
/**
   @property varchar $name name
@property varchar $slug slug
@property mediumtext $content content
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property timestamp $deleted_at deleted at
   
 */
class EmailTemplate extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    /**
    * Database table name
    */
    protected $table = 'email_templates';

    /**
    * Mass assignable columns
    */
    protected $fillable=['name','slug','content'];

    /**
    * Date time columns.
    */
    protected $dates=[];




}