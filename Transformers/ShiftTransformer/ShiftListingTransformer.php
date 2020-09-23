<?php
namespace App\Transformers\ShiftTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\tbleventsShifthour;
use App\Transformers\EventTransformer\TbleventdateTransformer;
use App\Transformers\EventTransformer\TbleventShortDetailTransformer;
use App\Transformers\PositionTransformer;
use App\Transformers\PeopleTransformer;
use EventHelper;

class ShiftListingTransformer extends TransformerAbstract
{
     /**
     * @var array
     */
    private $validParams = ['q', 'limit', 'page','fields'];

    /**
     * @var array
     */
    protected $availableIncludes = [
			'date','event','position','people'
		];

     /**
      * @var array
      */
    protected $defaultIncludes = [
			
		];


    public function transform(TbleventsShifthour $tbleventsShifthour){
			$encripted_id = EventHelper::base62encode($tbleventsShifthour->ID);
    	$data = [
							"ID" => $tbleventsShifthour->ID,
							"encripted_id" => $encripted_id,
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
							];
			$data['from'] = '';
			if($tbleventsShifthour->Start3){
				$data['from'] = date('H:i', strtotime($tbleventsShifthour->Start3));
			}
			if($tbleventsShifthour->Start2){
				$data['from'] = date('H:i', strtotime($tbleventsShifthour->Start2));
			}
			if($tbleventsShifthour->Start1){
				$data['from'] = date('H:i', strtotime($tbleventsShifthour->Start1));
			}

			$data['To'] = '';
			if($tbleventsShifthour->Finish1){
				$data['To'] = date('H:i', strtotime($tbleventsShifthour->Finish1));
			}
			if($tbleventsShifthour->Finish2){
				$data['To'] = date('H:i', strtotime($tbleventsShifthour->Finish2));
			}
			if($tbleventsShifthour->Finish3){
				$data['To'] = date('H:i', strtotime($tbleventsShifthour->Finish3));
			}
			
			return $this->filterFields($data);
	}

	/*Date Relation with Schedule get ITEM*/
	public function includeDate(tbleventsShifthour $entity){
		if( $entity->date != NULL ){
			return $this->item($entity->date, new TbleventdateTransformer());
		}
		return null;
	}
	
	/*Event Relation with Shift get ITEM*/
	public function includeEvent(TbleventsShifthour $entity){
		if( $entity->event != NULL ){
			return $this->item($entity->event, new TbleventShortDetailTransformer());
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

	/*People Relation with Schedule get ITEM*/
	public function includePeople(TbleventsShifthour $entity){
		if( $entity->people != NULL ){
			return $this->item($entity->people, new PeopleTransformer());
		}
		return null;
	}

}