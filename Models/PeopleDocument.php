<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PeopleDocument extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'people_documents';
    protected $fillable=['people_id', 'title', 'discription', 'file_id','user_type'];

    protected $primaryKey = 'id';
    
    /**
    * Date time columns.
    */

    protected $dates=['created_at'];
    
    public function FileDetails(){
        return $this->hasOne(File::class, 'id', 'file_id');
    }
}
