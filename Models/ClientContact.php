<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
   @property int $client_id client id
@property varchar $name name
@property varchar $title title
@property varchar $email email
@property varchar $home_phone home phone
@property varchar $work_phone work phone
@property varchar $cell_phone cell phone
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property timestamp $deleted_at deleted at
   
 */
class ClientContact extends Model 
{

    use SoftDeletes;    
    /**
    * Database table name
    */
    protected $table = 'client_contacts';

    /**
    * Mass assignable columns
    */
    protected $fillable=['ClientId','ContactName','ContactTitle','Email','Home','Work','WorkExt','Cell', 'Ext', 'Pager', 'Fax'];

    /**
    * Date time columns.
    */
    protected $dates=[];
    protected $primaryKey = 'ContactId';




}