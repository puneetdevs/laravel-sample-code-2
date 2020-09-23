<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HipsterJazzbo\Landlord\BelongsToTenants;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
   @property varchar $notification_type notification type
@property varchar $subject subject
@property longtext $message message
@property varchar $message_type message type
@property varchar $send_to send to
@property int $send_to_id send to id
@property varchar $send_by send by
@property int $send_by_id send by id
@property int $object_id object id
@property int $object_type object type
@property datetime $created_at created at
@property datetime $updated_at updated at
@property datetime $deleted_at deleted at
   
 */
class NotificationLog extends Model 
{
    use BelongsToTenants, SoftDeletes;
    /**
    * Database table name
    */
    protected $table = 'notification_logs';
    public $tenantColumns = ['region_id'];
    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'notification_type',
        'subject',
        'message',
        'message_type',
        'send_to',
        'send_to_id',
        'send_by',
        'send_by_id',
        'object_id',
        'object_type',
        'region_id'
    ];

    /**
    * Date time columns.
    */
    protected $dates=[];




}