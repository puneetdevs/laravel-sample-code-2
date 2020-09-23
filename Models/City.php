<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;

class City extends Model
{
    use SoftDeletes;
    use BelongsToTenants;
    protected $table = 'city';
    public $tenantColumns = ['region_id'];

    /**
    * Date time columns.
    */
    protected $dates = ['deleted_at'];

     /**
    * Mass assignable columns
    */
    protected $fillable = ['City', 'Filter','region_id'];
    protected $primaryKey = 'ID';

}
