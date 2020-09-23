<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;
use OwenIt\Auditing\Contracts\Auditable;

class DataFileSettings extends Model implements Auditable
{
    use SoftDeletes;
    use BelongsToTenants;
    use \OwenIt\Auditing\Auditable;
    /**
    * Database table name
    */
    protected $table = 'data_file_settings';
    public $tenantColumns = ['region_id'];

    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'CompanyName',
        'Address', 
        'labour_surcharge',
        'Province',
        'Phone',
        'Fax',
        'From',
        'Email',
        'OtherPhone',
        'Gst',
        'Col2',
        'EventRecord',
        'DateRecord',
        'PeopleRecord',
        'File',
        'version',
        'Format',
        'DefArea',
        'RegionCode',
        'ProvincalLabourCode',
        'InvoicePath',
        'FirstDayofYear',
        'PayrollPeriod',
        'OFARatePay',
        'OFARateInvoice',
        'Tax1_Name',
        'Tax1_Rate',
        'Tax1_RegNum',
        'Tax1_ApplyLabour',
        'Tax2_Name',
        'Tax2_Rate',
        'Tax2_RegNum',
        'Tax2_ApplyLabour',
        'Tax3_Name',
        'Tax3_Rate',
        'Tax3_RegNum',
        'Tax3_ApplyLabour',
        'Default_AS_Amount',
        'Default_AS_Tax1',
        'Default_AS_Tax2',
        'Default_AS_Tax3',
        'PhotoPath',
        'PDFPrinter',
        'PhotoDevice',
        'WeeklyOTHours',
        'Currency',
        'EmployeeCounter',
        'EventCounter',
        'reminder_time_off_start',
        'reminder_time_off_end',
        'reminder_send_before_time',
        'time_zone',
        'region_id'
    ];

    public function getReminderTimeOffStartAttribute($value)
    {
        return !is_null($value) ? date('H:i', strtotime($value)) : '';
    }
    public function getReminderTimeOffEndAttribute($value)
    {
        return !is_null($value) ? date('H:i', strtotime($value)) : '';
    }

    /**
    * Date time columns.
    */
    protected $dates=[];
    protected $primaryKey = 'ID';

}
