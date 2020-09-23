<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\LocationPhone;



class LocationPhoneTransformer extends TransformerAbstract
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


    public function transform(LocationPhone $locationPhone)
    {
        $data= [
			"id" => $locationPhone->ID,
			"location_id" => $locationPhone->VID,
			"description" => $locationPhone->PhoneText,
            "phone" => $locationPhone->PhoneNumber,
            "email" => $locationPhone->Email,
            "name" => $locationPhone->Name,
            "position" => $locationPhone->Position
            ];
        return $this->filterFields($data);

    }

    
}