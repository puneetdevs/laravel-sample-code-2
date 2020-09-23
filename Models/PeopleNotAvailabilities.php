<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PeopleNotAvailabilities extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'people_not_available';
    protected $fillable= ['StartDate', 'EndDate', 'Reason', 'peopleId'];

    protected $primaryKey = 'ID';
    
    /**
    * Date time columns.
    */

    protected $dates=['created_at'];
}
