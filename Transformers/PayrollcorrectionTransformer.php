<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Payrollcorrection;
use App\Transformers\LocationTransformer;
use App\Transformers\UserShortTransformer;
use App\Transformers\EventTransformer\TbleventShortTransformer;


class PayrollcorrectionTransformer extends TransformerAbstract
{
     /**
     * @var array
     */
    private $validParams = ['q', 'limit', 'page','fields'];

    /**
     * @var array
     */
    protected $availableIncludes = [
		'event','location','user'
	];

     /**
      * @var array
      */
    protected $defaultIncludes = [];


    public function transform(Payrollcorrection $payrollcorrection)
    {
        $data= [
			"ID" => $payrollcorrection->ID,
			"PeopleID" => $payrollcorrection->PeopleID,
			"VID" => $payrollcorrection->VID,
			"EID" => $payrollcorrection->EID,
			"PayPeriod_Start" => $payrollcorrection->PayPeriod_Start,
			"PayPeriod_End" => $payrollcorrection->PayPeriod_End,
			"Correction" => number_format((float)$payrollcorrection->Correction, 2, '.', ''),
			"correction_status" => $payrollcorrection->correction_status,
			"Submitter" => $payrollcorrection->Submitter,
			"Code" => $payrollcorrection->Code,
			"Comments" => $payrollcorrection->Comments,
			"ReasonAmount" => $payrollcorrection->ReasonAmount,
			"ReasonTimesheet" => $payrollcorrection->ReasonTimesheet,
			"ReasonPayroll" => $payrollcorrection->ReasonPayroll,
			"ReasonInvoice" => $payrollcorrection->ReasonInvoice,
			"ReasonInvoiceDate" => $payrollcorrection->ReasonInvoiceDate,
			"ReasonOther" => $payrollcorrection->ReasonOther,
			"SubmittedDate" => $payrollcorrection->SubmittedDate,
			"HoursIncorrect" => $payrollcorrection->HoursIncorrect,
			"created_at" => $payrollcorrection->created_at,
			"updated_at" => $payrollcorrection->updated_at,
			"deleted_at" => $payrollcorrection->deleted_at,

        ];
        return $this->filterFields($data);

    }

	/*Sales Manager Relation with Event get ITEM*/
	public function includeUser(Payrollcorrection $entity){
		if( $entity->user != NULL ){
			return $this->item($entity->user, new UserShortTransformer());
		}
		return null;
	}

	/*Event Manager Relation with Event get ITEM*/
	public function includeEvent(Payrollcorrection $entity){
		if( $entity->event != NULL ){
			return $this->item($entity->event, new TbleventShortTransformer());
		}
		return null;
	}

	/*Location Relation with Event get ITEM*/
	public function includeLocation(Payrollcorrection $entity){
		if( $entity->location != NULL ){
			return $this->item($entity->location, new LocationTransformer());
		}
		return null;
	}
    
}