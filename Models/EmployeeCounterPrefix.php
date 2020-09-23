<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use HipsterJazzbo\Landlord\BelongsToTenants;
use OwenIt\Auditing\Contracts\Auditable;

class EmployeeCounterPrefix extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    //use BelongsToTenants;
    protected $table = 'employee_counter_prefix';
    public $tenantColumns = ['region_id'];
    /**
    * Mass assignable columns
    */
    protected $fillable=['Emp_Cntr_Description', 'Emp_Cntr_Code', 'region_id'];
    protected $primaryKey = 'ID';
}
