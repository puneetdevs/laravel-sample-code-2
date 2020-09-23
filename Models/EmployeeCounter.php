<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use HipsterJazzbo\Landlord\BelongsToTenants;

class EmployeeCounter extends Model
{
    use SoftDeletes;
    use BelongsToTenants;
    protected $table = 'employee_counter';
    public $tenantColumns = ['region_id'];
    /**
    * Mass assignable columns
    */
    protected $fillable=['Value','region_id'];
    protected $primaryKey = 'ID';
}
