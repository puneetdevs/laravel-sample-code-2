<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use OwenIt\Auditing\Contracts\Auditable;

class File extends Model 
{
    //use \OwenIt\Auditing\Auditable;
    protected $table = 'files';
    

    /**
    * Mass assignable columns
    */
    protected $fillable=['path', 'name', 'file_type', 'object_type', 'user_type', 'object_id', 'upload_by'];

    /**
    * Date time columns.
    */
    protected $dates=[];

}
