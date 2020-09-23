<?php
namespace App\Transformers\EventTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\TbleventWaitingList;
use App\Transformers\EmployeeTransformer\EmployeeFilterTransformer;


class WaitingListTransformer extends TransformerAbstract
{
     /**
     * @var array
     */
    private $validParams = ['q', 'limit', 'page','fields'];

    /**
     * @var array
     */
    protected $availableIncludes = [
		'people'
	];

     /**
      * @var array
      */
    protected $defaultIncludes = [];


    public function transform(TbleventWaitingList $tbleventWaitingList){
    	$data = [
			"ID" => $tbleventWaitingList->ID,
			"EID" => $tbleventWaitingList->EID,
			"PeopleID" => $tbleventWaitingList->PeopleID,
			"PID" => $tbleventWaitingList->PID,
			"DID" => $tbleventWaitingList->DID,
			"con" => $tbleventWaitingList->con,
			"offer_send" => $tbleventWaitingList->offer_send,
			"created_at" => $tbleventWaitingList->created_at,
			"updated_at" => $tbleventWaitingList->updated_at,
			"deleted_at" => $tbleventWaitingList->deleted_at

        ];
        return $this->filterFields($data);

	}

	/*People Manager Relation with Waiting List get ITEM*/
	public function includePeople(TbleventWaitingList $entity){
		if( $entity->people != NULL ){
			return $this->item($entity->people, new EmployeeFilterTransformer());
		}
		return null;
    }
}