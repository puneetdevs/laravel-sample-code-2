<?php
namespace App\Transformers\EventTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\EventSchedule;
use App\Models\TbleventsShifthour;
use App\Transformers\PositionTransformer;
use App\Transformers\EventTransformer\TbleventdateTransformer;

class ScheduleTransformer extends TransformerAbstract
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
			"quantity" => $EventSchedule->quantity,
			"start_one" => $EventSchedule->start_one,
			"finish_one" => $EventSchedule->finish_one,
			"start_two" => $EventSchedule->start_two,
			"finish_two" => $EventSchedule->finish_two,
			"start_three" => $EventSchedule->start_three,
			"finish_three" => $EventSchedule->finish_three,
			"created_at" => $EventSchedule->created_at,
			"updated_at" => $EventSchedule->updated_at,
			"deleted_at" => $EventSchedule->deleted_at,
			"remaining" => $remaining

        ];
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