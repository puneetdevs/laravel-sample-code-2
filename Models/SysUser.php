<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;


class SysUser extends Model
{
    protected $table = "users";
    
    use SoftDeletes;
    use BelongsToTenants;
    
    public $timestamps = false;

    protected $fillable = [
        'fullname',
        'username',
        'email',
        'phonenumber',
        'password',
        'team_id'
    ];
}
