<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SysEmailConfig extends Model
{
    protected $table = "sys_emailconfig";

    public $timestamps = false;

    protected $fillable = [
        'team_id',
        'created_by',
        'method',
        'host',
        'username',
        'password',
        'apikey',
        'port',
        'secure'
    ];
}
