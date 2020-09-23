<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
   @property text $note note
@property varchar $object_type object type
@property int $object_id object id
@property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class Note extends Model implements Auditable 
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    /**
    * Database table name
    */
    protected $table = 'notes';
    


    /**
    * Mass assignable columns
    */
    protected $fillable=['Note', 'ParentCode', 'ParentID', 'NoteDate','AddedBy'];

    /**
    * Date time columns.
    */
    protected $dates=[];

    public function addedBy(){
        return $this->hasOne('App\User', 'id', 'AddedBy');
    }



}