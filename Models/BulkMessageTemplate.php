<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HipsterJazzbo\Landlord\BelongsToTenants;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
/**
    @property varchar $title title
    @property varchar $type type
    @property varchar $subject subject
    @property longtext $message message
    @property datetime $created_at created at
    @property datetime $updated_at updated at
    @property datetime $deleted_at deleted at
   
 */
class BulkMessageTemplate extends Model implements Auditable
{
    
    /**
    * Database table name
    */
    protected $table = 'bulk_message_template';
    public $tenantColumns = ['region_id'];
    use \OwenIt\Auditing\Auditable;

    /**
    * Mass assignable columns
    */
    protected $fillable=['title',
        'region_id',
        'type',
        'subject',
        'message'
    ];

    /**
    * Date time columns.
    */
    protected $dates=[];

}