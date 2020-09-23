<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;
use OwenIt\Auditing\Contracts\Auditable;

class Configuration extends Model implements Auditable
{
    use SoftDeletes;
    use BelongsToTenants;
    use \OwenIt\Auditing\Auditable;

    /**
    * Database table name
    */
    protected $table = 'configurations';
    public $tenantColumns = ['region_id'];

    /**
    * Mass assignable columns
    */
    protected $fillable=['ConfigName', 'THBegins', 'THEnds', 'SpecOTStart', 'SpecOTEnd', 'BreakPeriod', 'OverTimeCalc', 'OTStarts', 'DTStarts', 'BreakPeriodAcross', 'Country', 'Prov', 'OFASpecialRate'];

    /**
    * Date time columns.
    */
    protected $dates=['created_at', 'updated_at', 'deteted_at'];
    
    protected $primaryKey = 'CfgID';

    
}
