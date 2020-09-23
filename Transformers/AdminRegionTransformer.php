<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Region;



class AdminRegionTransformer extends TransformerAbstract
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


    public function transform(Region $region)
    {
        $data= [
			"id" => $region->id,
            "name" => $region->name,
            "created_at" => $region->created_at,
            "updated_at" => $region->updated_at
        ];
        return $this->filterFields($data);

    }

    
}