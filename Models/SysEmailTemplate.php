<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SysEmailTemplate extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = "sys_email_templates";

    public $timestamps = false;

    protected $fillable = [
        'team_id',
        'created_by',
        'tplname',
        'language_id',
        'subject',
        'message',
        'send',
        'core',
        'hidden'
    ];
}
