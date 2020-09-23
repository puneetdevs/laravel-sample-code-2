<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Region extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'regions';
    

    /**
    * Mass assignable columns
    */
    protected $fillable=['name'];

    /**
    * Date time columns.
    */
    protected $dates=[];

    //Region Setting relation with Region
    public function region_setting(){
        return $this->hasOne(DataFileSettings::class,'region_id','id');
    }  

}
