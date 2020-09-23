<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
/**
@property int $EID EID
@property int $DID DID
@property varchar $subject subject
@property longtext $message message
@property datetime $created_at created at
@property datetime $updated_at updated at
@property datetime $deleted_at deleted at
 */
class OfferEmailTemplate extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    /**
    * Database table name
    */
    protected $table = 'offer_email_templates';

    
    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'EID',
        'DID',
        'subject',
        'message',
        'sms_message',
        'status'
    ];

    /**
    * Date time columns.
    */
    protected $dates=[];
}