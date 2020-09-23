<?php
namespace App\Transformers\EventTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Tblevent;
use App\Transformers\LocationTransformer;


class TbleventShortDetailTransformer extends TransformerAbstract
{
     /**
     * @var array
     */
    private $validParams = ['q', 'limit', 'page','fields'];

    /**
     * @var array
     */
    protected $availableIncludes = [];

     /**
      * @var array
      */
    protected $defaultIncludes = ['location'];


    public function transform(Tblevent $tblevent)
    {

        $data= [
					"EID" => $tblevent->EID,
					"region_id" => $tblevent->region_id,
					"EventName" => $tblevent->EventName,
					"VID" => $tblevent->VID,
					"Status" => $tblevent->Status,
					"EventNameShort" => $tblevent->EventNameShort,
					"AccountRep" => $tblevent->AccountRep,
					"sales_manager" => $tblevent->sales_manager,
					"created_at" => $tblevent->created_at,
					"updated_at" => $tblevent->updated_at,
					"deleted_at" => $tblevent->deleted_at
        ];
        return $this->filterFields($data);

	}

	/*Location Relation with Event get ITEM*/
	public function includeLocation(Tblevent $entity){
		if( $entity->location != NULL ){
			return $this->item($entity->location, new LocationTransformer());
		}
		return null;
	}
    
}