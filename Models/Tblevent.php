<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use HipsterJazzbo\Landlord\BelongsToTenants;
use OwenIt\Auditing\Contracts\Auditable;
use App\User;
use App\Models\Tblworkcategory;
use App\Models\Location;
use App\Models\Tbleventdate;
use App\Models\Configuration;
use App\Models\TbleventsInvoicelineitem;
use App\Models\Client;
use App\Models\Note;



/**
   @property int $region_id region id
@property varchar $EventName EventName
@property bigint $VID VID
@property bigint $CfgID CfgID
@property varchar $EventID EventID
@property bigint $ClientId ClientId
@property varchar $InternalNotes InternalNotes
@property varchar $ExternalNotes ExternalNotes
@property datetime $EventDateCreated EventDateCreated
@property varchar $EventCreatedBy EventCreatedBy
@property datetime $EventDateLastUpdated EventDateLastUpdated
@property varchar $EventUpdatedBy EventUpdatedBy
@property smallint $Schedule Schedule
@property smallint $ShiftSort ShiftSort
@property smallint $Status Status
@property varchar $JobNumber JobNumber
@property varchar $UnitNumber UnitNumber
@property varchar $ItemNumber ItemNumber
@property varchar $PONumber PONumber
@property decimal $POCap POCap
@property tinyint $ShortList ShortList
@property tinyint $Booked Booked
@property tinyint $Filter Filter
@property varchar $Tax1_Name Tax1 Name
@property float $Tax1_Rate Tax1 Rate
@property varchar $Tax1_RegNum Tax1 RegNum
@property tinyint $Tax1_ApplyLabour Tax1 ApplyLabour
@property varchar $Tax2_Name Tax2 Name
@property float $Tax2_Rate Tax2 Rate
@property varchar $Tax2_RegNum Tax2 RegNum
@property tinyint $Tax2_ApplyLabour Tax2 ApplyLabour
@property float $AS_Amount AS Amount
@property tinyint $AS_Tax1 AS Tax1
@property tinyint $AS_Tax2 AS Tax2
@property int $WorkCategoryID WorkCategoryID
@property varchar $AccountRep AccountRep
@property varchar $SalesRep SalesRep
@property varchar $EventNameShort EventNameShort
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property timestamp $deleted_at deleted at
   
 */
class Tblevent extends Model implements Auditable
{
    use SoftDeletes;
    use BelongsToTenants;
    use \OwenIt\Auditing\Auditable;
    
    public $tenantColumns = ['region_id'];
    /**
    * Database table name
    */
    protected $table = 'events';

    /**
    * Mass assignable columns
    */
    protected $fillable=[
        'EventName',
        'VID',
        'CfgID',
        'EventID',
        'ClientId',
        'InternalNotes',
        'ExternalNotes',
        'EventDateCreated',
        'EventCreatedBy',
        'labor_surcharge',
        'EventDateLastUpdated',
        'EventUpdatedBy',
        'Schedule',
        'ShiftSort',
        'Status',
        'JobNumber',
        'UnitNumber',
        'ItemNumber',
        'PONumber',
        'POCap',
        'ShortList',
        'Booked',
        'Invoiced',
        'Filter',
        'Tax1_Name',
        'Tax1_Rate',
        'Tax1_RegNum',
        'Tax1_ApplyLabour',
        'Tax2_Name',
        'Tax2_Rate',
        'Tax2_RegNum',
        'Tax2_ApplyLabour',
        'AS_Amount',
        'AS_Tax1',
        'sales_manager',
        'account_manager',
        'AS_Tax2',
        'WorkCategoryID',
        'AccountRep',
        'SalesRep',
        'autoconfirm',
        'EventNameShort'];

    /**
    * Date time columns.
    */
    protected $dates=['EventDateCreated',
'EventDateLastUpdated'];

protected $primaryKey = 'EID';

/*Relation with Event Sales Manager and user*/
public function sales()
{
    return $this->hasOne(User::class, 'id', 'sales_manager');
}

/*Relation with Event Account Manager and user*/
public function account()
{
    return $this->hasOne(User::class, 'id', 'account_manager');
}

/*Relation with Event Work Category*/
public function work()
{
    return $this->hasOne(Tblworkcategory::class, 'ID', 'WorkCategoryID');
}

/*Relation with Location*/
public function location()
{
    return $this->hasOne(Location::class, 'VID', 'VID');
}

/*Relation with Date*/
public function date()
{
    return $this->hasMany(Tbleventdate::class, 'EID', 'EID');
}

//shift relation with event
public function shift(){
    return $this->hasMany(TbleventsShifthour::class,'EID','EID');
}

//client relation with event
public function client(){
    return $this->hasOne(Client::class,'ID','ClientId');
}

//note relation with event
public function note(){
    return $this->hasMany(Note::class,'ParentID','EID');
}

//configration relation with event
public function configration(){
    return $this->hasOne(Configuration::class,'CfgID','CfgID');
}

//Line Item relation with event
public function lineitem(){
    return $this->hasMany(TbleventsInvoicelineitem::class,'EID','EID');
}



}