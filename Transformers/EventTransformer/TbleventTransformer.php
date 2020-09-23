<?php
namespace App\Transformers\EventTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Tblevent;
use App\User;
use App\Models\Tbleventdate;
use App\Transformers\UserTransformer;
use App\Transformers\TblworkcategoryTransformer;
use App\Transformers\ClientTransformer;
use App\Transformers\LocationTransformer;

class TbleventTransformer extends TransformerAbstract
{
     /**
     * @var array
     */
    private $validParams = ['q', 'limit', 'page','fields'];

    /**
     * @var array
     */
    protected $availableIncludes = [
		'sales','account','work','client','location'
	];

     /**
      * @var array
      */
    protected $defaultIncludes = [];


    public function transform(Tblevent $tblevent)
    {

		$first_date = '';
		$first_date_q = Tbleventdate::where('EID',  $tblevent->EID)->orderBy('Eventdate','ASC')->first();
		if($first_date_q){
            $first_date = $first_date_q->Eventdate;
		}
		$last_date = '';
		$last_date_q = Tbleventdate::where('EID',  $tblevent->EID)->orderBy('Eventdate','DESC')->first();
		if($last_date_q){
            $last_date = $last_date_q->Eventdate;
		}

		// get previous user id
		$previous = Tblevent::where('EID', '<', $tblevent->EID)->where('region_id', $tblevent->region_id)->max('EID');

		// get next user id
		$next = Tblevent::where('EID', '>', $tblevent->EID)->where('region_id', $tblevent->region_id)->min('EID');

		// get previous user id
		$EventUpdatedByName = '';
		
		if($tblevent->EventUpdatedBy){
			$user_data = User::where('id', $tblevent->EventUpdatedBy)->first();
			if($user_data){
				$EventUpdatedByName = $user_data->fullname;
			}
		}

		$EventCreatedByName = '';
		if($tblevent->EventCreatedBy){
			$user_data1 = User::where('id', $tblevent->EventCreatedBy)->first();
			if($user_data1){
				$EventCreatedByName = $user_data1->fullname;
			}
		}
		$first_display_date = $first_date != '' ? ' ('.date('Y-m-d',strtotime($first_date)) : '';
		$last_display_date = $last_date != '' ? '-'.date('Y-m-d',strtotime($last_date)).')' : '';
		$display_name = $tblevent->EventName.$first_display_date.$last_display_date;
        $data= [
			"EID" => $tblevent->EID,
			"region_id" => $tblevent->region_id,
			"EventName" => $tblevent->EventName,
			"VID" => $tblevent->VID,
			"CfgID" => $tblevent->CfgID,
			"EventID" => $tblevent->EventID,
			"ClientId" => $tblevent->ClientId,
			"InternalNotes" => $tblevent->InternalNotes,
			"labor_surcharge" => $tblevent->labor_surcharge,
			"first_date" => $first_date,
			"last_date" => $last_date,
			"ExternalNotes" => $tblevent->ExternalNotes,
			"EventDateCreated" => $tblevent->EventDateCreated,
			"EventCreatedByName" => $EventCreatedByName,
			"EventCreatedBy" => $tblevent->EventCreatedBy,
			"EventDateLastUpdated" => $tblevent->EventDateLastUpdated,
			"EventUpdatedBy" => $tblevent->EventUpdatedBy,
			"EventUpdatedByName" => $EventUpdatedByName,
			"Schedule" => $tblevent->Schedule,
			"ShiftSort" => $tblevent->ShiftSort,
			"Status" => $tblevent->Status,
			"JobNumber" => $tblevent->JobNumber,
			"UnitNumber" => $tblevent->UnitNumber,
			"ItemNumber" => $tblevent->ItemNumber,
			"PONumber" => $tblevent->PONumber,
			"POCap" => $tblevent->POCap,
			"ShortList" => $tblevent->ShortList,
			"Booked" => $tblevent->Booked,
			"Invoiced" => $tblevent->Invoiced,
			"Filter" => $tblevent->Filter,
			"Tax1_Name" => $tblevent->Tax1_Name,
			"Tax1_Rate" => $tblevent->Tax1_Rate,
			"Tax1_RegNum" => $tblevent->Tax1_RegNum,
			"Tax1_ApplyLabour" => $tblevent->Tax1_ApplyLabour,
			"Tax2_Name" => $tblevent->Tax2_Name,
			"Tax2_Rate" => $tblevent->Tax2_Rate,
			"Tax2_RegNum" => $tblevent->Tax2_RegNum,
			"Tax2_ApplyLabour" => $tblevent->Tax2_ApplyLabour,
			"AS_Amount" => $tblevent->AS_Amount,
			"AS_Tax1" => $tblevent->AS_Tax1,
			"AS_Tax2" => $tblevent->AS_Tax2,
			"sales_manager" => $tblevent->sales_manager,
			"account_manager" => $tblevent->account_manager,
			"WorkCategoryID" => $tblevent->WorkCategoryID,
			"AccountRep" => $tblevent->AccountRep,
			"SalesRep" => $tblevent->SalesRep,
			"autoconfirm" => $tblevent->autoconfirm,
			"EventNameShort" => $display_name,
			"created_at" => $tblevent->created_at,
			"updated_at" => $tblevent->updated_at,
			"deleted_at" => $tblevent->deleted_at,
			"next_event" => $next,
			"previous_event" => $previous,

        ];
        return $this->filterFields($data);

	}
	
	/*Sales Manager Relation with Event get ITEM*/
	public function includeSales(Tblevent $entity){
		if( $entity->sales != NULL ){
			return $this->item($entity->sales, new UserTransformer());
		}
		return null;
	}

	/*Account Manager Relation with Event get ITEM*/
	public function includeAccount(Tblevent $entity){
		if( $entity->account != NULL ){
			return $this->item($entity->account, new UserTransformer());
		}
		return null;
	}

	/*Work Category Relation with Event get ITEM*/
	public function includeWork(Tblevent $entity){
		if( $entity->work != NULL ){
			return $this->item($entity->work, new TblworkcategoryTransformer());
		}
		return null;
	}

	/*Client Relation with Event get ITEM*/
	public function includeClient(Tblevent $entity){
		if( $entity->client != NULL ){
			return $this->item($entity->client, new ClientTransformer());
		}
		return null;
	}

	/*Location Relation with Event get ITEM*/
	public function includeLocation(Tblevent $entity){
		if( $entity->location != NULL ){
			return $this->item($entity->location, new LocationTransformer());
		}
		return null;
	}

    
}