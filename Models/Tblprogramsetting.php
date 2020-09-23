<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tblprogramsetting extends Model
{
    use SoftDeletes;
    

    /**
    * Database table name
    */
    protected $table = 'program_settings';
    

    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'ExcessInvoiceRate_Flag', 
        'ExcessPayrollRate_Flag', 
        'ExcessPay_Flag', 
        'ShortHours_Flag', 
        'ShortInvoiceRate_Flag',
        'ExcessFlatRate_Invoice',
        'ExcessFlatRate_Payroll'
    ];

    
    protected $primaryKey = 'ID';
}
