<?php
namespace App\Transformers\EventTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Tblevent;
use App\Transformers\LocationTransformer;
use App\Transformers\UserTransformer;
use App\Transformers\EventTransformer\NoteDetailTransformer;
use App\Transformers\ClientTransformer;

class TbleventDetailTransformer extends TransformerAbstract
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
    protected $defaultIncludes = ['location','sales','account','note','client'];


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

	/*Account Manager Relation with Event get ITEM*/
	public function includeNote(Tblevent $entity){
		if( $entity->note != NULL ){
			return $this->collection($entity->note, new NoteDetailTransformer());
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
    
}