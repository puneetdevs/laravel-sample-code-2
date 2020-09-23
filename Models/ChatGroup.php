<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
/**
   @property varchar $group_uuid group uuid
@property int $event_id event id
@property int $created_by created by
@property varchar $group_name group name
@property tinyint $type type
@property datetime $created_at created at
@property datetime $updated_at updated at
@property datetime $deleted_at deleted at
   
 */
class ChatGroup extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    /**
    * Database table name
    */
    protected $table = 'chat_group';

    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'group_uuid',
        'event_id',
        'created_by',
        'group_name',
        'type'
    ];

    /**
    * Date time columns.
    */
    protected $dates=[];

}