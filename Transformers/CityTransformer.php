<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\City;



class CityTransformer extends TransformerAbstract
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


    public function transform(City $city)
    {
        $data= [
			"id" => $city->ID,
            "name" => $city->City,
            "Filter" => $city->Filter,
			"created_at" => $city->created_at,
			"updated_at" => $city->updated_at,
			"deleted_at" => $city->deleted_at,

        ];
        return $this->filterFields($data);

    }

    
}