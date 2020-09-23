<?php
namespace App\Transformers\GroupTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\TblgroupPeople;
use App\Transformers\EmployeeTransformer\EmployeeFilterTransformer;



class TblgroupPeopleTransformer extends TransformerAbstract
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


    public function transform(TblgroupPeople $tblgroupPeople)
    {
        $data= [
			"ID" => $tblgroupPeople->ID,
			"GID" => $tblgroupPeople->GID,
            "PeopleID" => $tblgroupPeople->PeopleID,
            "Pre" => $tblgroupPeople->Pre,
			"created_at" => $tblgroupPeople->created_at,
			"updated_at" => $tblgroupPeople->updated_at,
			"deleted_at" => $tblgroupPeople->deleted_at,

        ];
        return $this->filterFields($data);

    }

    /*People Manager Relation with Event get ITEM*/
	public function includePeople(TblgroupPeople $entity){
		if( $entity->people != NULL ){
			return $this->item($entity->people, new EmployeeFilterTransformer());
		}
		return null;
	}
    
}