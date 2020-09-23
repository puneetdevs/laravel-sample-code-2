<?php
namespace App\Transformers\EventTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\EventSchedule;
use App\Transformers\PositionTransformer;
use App\Models\TbleventsShifthour;
use App\Transformers\EventTransformer\TbleventdateTransformer;

class ShiftSummaryTransformer extends TransformerAbstract
{
     /**
     * @var array
     */
    private $validParams = ['q', 'limit', 'page','fields'];

    /**
     * @var array
     */
    protected $availableIncludes = [
		'position','date'
	];

     /**
      * @var array
      */
    protected $defaultIncludes = [];


    public function transform(EventSchedule $EventSchedule){
		$remaining_count = TbleventsShifthour::where('EID',$EventSchedule->event_id)
											->where('DID',$EventSchedule->date_id)
											->where('PID',$EventSchedule->position_id)
											->where('schedule_id',$EventSchedule->schedule_id)
											->where('Confirmed',1)->count();

		$remaining = $EventSchedule->quantity;	
		if($remaining_count){
			$remaining = $EventSchedule->quantity - $remaining_count;
		}
    	$data = [
			"id" => $EventSchedule->id,
			"event_id" => $EventSchedule->event_id,
			"date_id" => $EventSchedule->date_id,
			"position_id" => $EventSchedule->position_id,
			"total" => $EventSchedule->quantity,
			"remaining" => $remaining
		];
		$data['from'] = '';
		if($EventSchedule->start_three){
			$data['from'] = date('H:i', strtotime($EventSchedule->start_three));
		}
		if($EventSchedule->start_two){
			$data['from'] = date('H:i', strtotime($EventSchedule->start_two));
		}
		if($EventSchedule->start_one){
			$data['from'] = date('H:i', strtotime($EventSchedule->start_one));
		}

		$data['To'] = '';
		if($EventSchedule->finish_one){
			$data['To'] = date('H:i', strtotime($EventSchedule->finish_one));
		}
		if($EventSchedule->finish_two){
			$data['To'] = date('H:i', strtotime($EventSchedule->finish_two));
		}
		if($EventSchedule->finish_three){
			$data['To'] = date('H:i', strtotime($EventSchedule->finish_three));
		}
        return $this->filterFields($data);

	}

	/*Position Relation with Schedule get ITEM*/
	public function includePosition(EventSchedule $entity){
		if( $entity->position != NULL ){
			return $this->item($entity->position, new PositionTransformer());
		}
		return null;
	}

	/*Date Relation with Schedule get ITEM*/
	public function includeDate(EventSchedule $entity){
		if( $entity->date != NULL ){
			return $this->item($entity->date, new TbleventdateTransformer());
		}
		return null;
	}
}