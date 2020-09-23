<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HipsterJazzbo\Landlord\BelongsToTenants;
use Illuminate\Database\Eloquent\SoftDeletes;
class EventCounter extends Model
{
    use SoftDeletes;
    use BelongsToTenants;
    protected $table = 'event_counter';
    public $tenantColumns = ['region_id'];
    /**
    * Mass assignable columns
    */
    protected $fillable=['Value', 'region_id'];
    protected $primaryKey = 'ID';
}
