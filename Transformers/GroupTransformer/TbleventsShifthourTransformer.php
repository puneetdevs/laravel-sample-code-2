<?php
namespace App\Transformers\GroupTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\TbleventsShifthour;
use App\Models\PeopleNotAvailabilities;
use App\Transformers\PositionTransformer;
use App\Transformers\EmployeeTransformer\EmployeeFilterTransformer;
use App\Transformers\EventTransformer\TbleventdateTransformer;
use App\Transformers\DepartmentTransformer;



class TbleventsShifthourTransformer extends TransformerAbstract
{
    protected $date_ids;

    public function __construct($date_ids = null) {
        $this->date_ids = $date_ids;
    }

     /**
     * @var array
     */
    private $validParams = ['q', 'limit', 'page','fields'];

    /**
     * @var array
     */
    protected $availableIncludes = [
        'people','position', 'department','date'
    ];

     /**
      * @var array
      */
    protected $defaultIncludes = [];


    public function transform(TbleventsShifthour $tbleventsShifthour)
    {
        $not_available = array();
        if($this->date_ids){
            $not_avail_query = PeopleNotAvailabilities::where('peopleId',$tbleventsShifthour->PeopleID)->whereIn("StartDate", $this->date_ids)->get()->toArray();
            if(!empty($not_avail_query)){
                $not_available = $not_avail_query;
            }
        }

        $data= [
			"ID" => $tbleventsShifthour->ID,
			"PeopleID" => $tbleventsShifthour->PeopleID,
            "EID" => $tbleventsShifthour->EID,
            "PID" => $tbleventsShifthour->PID,
            "DID" => $tbleventsShifthour->DID,
            "Department" => $tbleventsShifthour->Department,
            "Quantity" => $tbleventsShifthour->Quantity,
            "Start1" => !is_null($tbleventsShifthour->Start1) ? date('H:i', strtotime($tbleventsShifthour->Start1)):'',
            "Finish1" => !is_null($tbleventsShifthour->Finish1) ? date('H:i', strtotime($tbleventsShifthour->Finish1)):'',
            "Start2" => !is_null($tbleventsShifthour->Start2) ? date('H:i', strtotime($tbleventsShifthour->Start2)):'',
            "Finish2" => !is_null($tbleventsShifthour->Finish2) ? date('H:i', strtotime($tbleventsShifthour->Finish2)):'',
            "Start3" => !is_null($tbleventsShifthour->Start3) ? date('H:i', strtotime($tbleventsShifthour->Start3)):'',
            "Finish3" => !is_null($tbleventsShifthour->Finish3) ? date('H:i', strtotime($tbleventsShifthour->Finish3)):'',
            "Confirmed" => $tbleventsShifthour->Confirmed,
            "Pre" => $tbleventsShifthour->Pre,
            'not_available' => $not_available,
            "shift_id" => '',
            "note" => $tbleventsShifthour->note,
            "SNS" => $tbleventsShifthour->SNS,
            "SOC" => $tbleventsShifthour->SOC,
            "OFA" => $tbleventsShifthour->OFA,
			"created_at" => $tbleventsShifthour->created_at,
			"updated_at" => $tbleventsShifthour->updated_at,
			"deleted_at" => $tbleventsShifthour->deleted_at,

        ];
        
        return $this->filterFields($data);

    }

    /*People Manager Relation with Event get ITEM*/
	public function includePeople(TbleventsShifthour $entity){
		if( $entity->people != NULL ){
			return $this->item($entity->people, new EmployeeFilterTransformer());
		}
		return null;
    }
    
    /*Position Relation with Schedule get ITEM*/
	public function includePosition(TbleventsShifthour $entity){
		if( $entity->position != NULL ){
			return $this->item($entity->position, new PositionTransformer());
		}
		return null;
    }

    /*Department Relation with Schedule get ITEM*/
	public function includeDepartment(TbleventsShifthour $entity){
		if( $entity->department != NULL ){
			return $this->item($entity->department, new DepartmentTransformer());
		}
		return null;
    }

    /*Date Relation with Schedule get ITEM*/
	public function includeDate(tbleventsShifthour $entity){
		if( $entity->date != NULL ){
			return $this->item($entity->date, new TbleventdateTransformer());
		}
		return null;
	}
    
    
}