<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class ConfigurationRates extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    /**
    * Database table name
    */
    protected $table = 'configurations_sub_rates';
    

    /**
    * Mass assignable columns
    */
    protected $fillable=['PID', 'CfgID', 'BasePay', 'BaseCharge', 'OtherCharge', 'Flat', 'EnablePayrollAdj'];

    /**
    * Date time columns.
    */
    protected $dates=['created_at', 'updated_at', 'deteted_at'];
    
    protected $primaryKey = 'ID';
    
    public function postion()
    {
        return $this->hasOne(Position::class, 'PID', 'PID');
    }
}
