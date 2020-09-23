<?php
namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\User;
/**
   @property bigint $PeopleID PeopleID
    @property bigint $VID VID
    @property bigint $EID EID
    @property int $Region Region
    @property varchar $PayPeriod PayPeriod
    @property decimal $Correction Correction
    @property varchar $Submitter Submitter
    @property varchar $Code Code
    @property varchar $Comments Comments
    @property tinyint $ReasonAmount ReasonAmount
    @property tinyint $ReasonTimesheet ReasonTimesheet
    @property tinyint $ReasonPayroll ReasonPayroll
    @property tinyint $ReasonInvoice ReasonInvoice
    @property datetime $ReasonInvoiceDate ReasonInvoiceDate
    @property varchar $ReasonOther ReasonOther
    @property datetime $SubmittedDate SubmittedDate
    @property datetime $created_at created at
    @property datetime $updated_at updated at
    @property datetime $deleted_at deleted at
 */

class Payrollcorrection extends Model implements Auditable 
{
    
    /**
    * Database table name
    */
    protected $table = 'payroll_corrections';
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'PeopleID',
        'VID',
        'EID',
        'Region',
        'PayPeriod_Start',
        'PayPeriod_End',
        'correction_status',
        'Correction',
        'Submitter',
        'Code',
        'Comments',
        'ReasonAmount',
        'ReasonTimesheet',
        'ReasonPayroll',
        'ReasonInvoice',
        'ReasonInvoiceDate',
        'ReasonOther',
        'SubmittedDate',
        'HoursIncorrect'
    ];

    /**
    * Date time columns.
    */
    protected $dates=[
        'ReasonInvoiceDate',
        'SubmittedDate'
    ];

    protected $primaryKey = 'ID';

    /*Relation with Location*/
    public function location()
    {
        return $this->hasOne(Location::class, 'VID', 'VID');
    }

    /*Relation with Location*/
    public function event()
    {
        return $this->hasOne(Tblevent::class, 'EID', 'EID');
    }

    /*Relation with Location*/
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'Submitter');
    }
}