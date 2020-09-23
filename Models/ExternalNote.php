<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ExternalNoteDetail;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;
use OwenIt\Auditing\Contracts\Auditable;

class ExternalNote extends Model implements Auditable
{
    protected $table = 'external_notes';
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'title', 
        'email_subject', 
        'email_message', 
        'email_template_id',
        'sms_message', 
        'sms_template_id',
        'email', 
        'sms', 
        'EID',
        'status'
    ];

    /**
    * Date time columns.
    */
    protected $dates=[];

    /*Relation with External detail*/
    public function external_detail()
    {
        return $this->hasOne(ExternalNoteDetail::class, 'ENID', 'id');
    }
    

}
