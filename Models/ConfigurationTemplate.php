<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;
/**
   @property varchar $ConfigName ConfigName
@property time $THBegins THBegins
@property time $THEnds THEnds
@property time $SpecOTStart SpecOTStart
@property time $SpecOTEnd SpecOTEnd
@property varchar $BreakPeriod BreakPeriod
@property smallint $OverTimeCalc OverTimeCalc
@property smallint $OTStarts OTStarts
@property smallint $DTStarts DTStarts
@property float $BreakPeriodAcross BreakPeriodAcross
@property tinyint $OFARate OFARate
@property float $OFABasePay OFABasePay
@property float $OFABaseCharge OFABaseCharge
@property float $OFAOtherCharge OFAOtherCharge
@property float $OFASpecialRate OFASpecialRate
@property float $BaseCharge BaseCharge
@property float $OtherCharge OtherCharge
@property int $region_id region id
@property datetime $created_at created at
@property datetime $updated_at updated at
@property datetime $deleted_at deleted at
@property varchar $Country Country
@property varchar $Prov Prov
   
 */
class ConfigurationTemplate extends Model 
{
    use SoftDeletes;
    use BelongsToTenants;
    /**
    * Database table name
    */
    protected $table = 'configuration_templates';
    public $tenantColumns = ['region_id'];
    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'Prov',
        'ConfigName',
        'THBegins',
        'THEnds',
        'SpecOTStart',
        'SpecOTEnd',
        'BreakPeriod',
        'OverTimeCalc',
        'OTStarts',
        'DTStarts',
        'BreakPeriodAcross',
        'OFARate',
        'OFABasePay',
        'OFABaseCharge',
        'OFAOtherCharge',
        'OFASpecialRate',
        'BaseCharge',
        'OtherCharge',
        'region_id',
        'Country',
        'Prov'
    ];

    /**
    * Date time columns.
    */
    protected $dates=['created_at', 'updated_at', 'deteted_at'];

    protected $primaryKey = 'ID';


}