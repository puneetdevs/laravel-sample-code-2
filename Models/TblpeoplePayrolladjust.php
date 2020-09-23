<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
@property int $PeopleID PeopleID
@property float $Value Value
@property datetime $DateEffectiveStart DateEffectiveStart
@property datetime $DateEffectiveEnd DateEffectiveEnd
@property varchar $payroll_variable_type payroll variable type
@property datetime $created_at created at
@property datetime $updated_at updated at
@property datetime $deleted_at deleted at
 */
class TblpeoplePayrolladjust extends Model 
{
    use SoftDeletes;

    /**
    * Database table name
    */
    protected $table = 'people_payroll_adjust';

    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'PeopleID',
        'Value',
        'DateEffectiveStart',
        'DateEffectiveEnd',
        'payroll_variable_type'
    ];

    /**
    * Date time columns.
    */
    protected $dates=[
        'DateEffectiveStart',
        'DateEffectiveEnd'
    ];

    protected $primaryKey = 'ID';

    public function people()
    {
        return $this->hasOne(People::class, 'PeopleID', 'PeopleID');
    }
}