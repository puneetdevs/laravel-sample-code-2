<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\People;

class PeopleTransformer extends TransformerAbstract
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
    protected $defaultIncludes = [];


    public function transform(People $people)
    {
       
        $data= [
			"PeopleID" => $people->PeopleID,
			"region_id" => $people->region_id,
            "user_id" => $people->user_id,
            
            "FirstName" => $people->FirstName,
            "LastName" => $people->LastName,
            "Initial" => $people->Initial,
            "Prov" => $people->Prov,
            "Suite" => $people->Suite,
            "AddressLine1" => $people->AddressLine1,
            "City" => $people->City,
            "Country" => $people->Country,
            "Postal" => $people->Postal,
			"created_at" => $people->created_at,
			"updated_at" => $people->updated_at,
			"deleted_at" => $people->deleted_at

        ];
        
        return $this->filterFields($data);

	}
    
}