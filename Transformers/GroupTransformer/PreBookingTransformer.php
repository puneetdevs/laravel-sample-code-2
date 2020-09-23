<?php
namespace App\Transformers\GroupTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\PreBooking;
use App\Transformers\EmployeeTransformer\EmployeeFilterTransformer;



class PreBookingTransformer extends TransformerAbstract
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


    public function transform(PreBooking $pre_booking)
    {
        $data= [
			"id" => $pre_booking->id,
			"EID" => $pre_booking->EID,
            "PeopleID" => $pre_booking->PeopleID,
            "DID" => $pre_booking->DID,
            "note" => $pre_booking->note,
			"created_at" => $pre_booking->created_at,
			"updated_at" => $pre_booking->updated_at,
			"deleted_at" => $pre_booking->deleted_at,

        ];
        return $this->filterFields($data);

    }

    /*People Manager Relation with Event get ITEM*/
	public function includePeople(PreBooking $entity){
		if( $entity->people != NULL ){
			return $this->item($entity->people, new EmployeeFilterTransformer());
		}
		return null;
	}
    
}