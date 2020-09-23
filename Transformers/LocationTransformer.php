<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Location;



class LocationTransformer extends TransformerAbstract
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


    public function transform(Location $location)
    {
        $data= [
			"id" => $location->VID,
			"name" => $location->VName,
			"phone" => $location->VenuePhone,
			"email" => $location->VenueEmail,
			"fax" => $location->VenueFax,
			"address_1" => $location->VAddressLine1,
			"address_2" => $location->VAddressLine2,
			"city" => $location->VCity,
			"providence" => $location->VProv,
			"postal_code" => $location->VPostal,
			"country" => $location->VCountry,
			"default_configuration_id" => $location->CfgID,
			"schedue" => $location->DefaultRateSchedule,
			"vanue_code" => $location->VenueCode,
            "directions" => $location->VDirections,
            "VLat" => ($location->VLat) ? floatval($location->VLat) : null,
            "VLng" => ($location->VLng) ? floatval($location->VLng) : null,
            "Website" => $location->Website,
            "status" => $location->status,
			"created_at" => $location->created_at,
			"updated_at" => $location->updated_at,
			"deleted_at" => $location->deleted_at,

        ];
        return $this->filterFields($data);

    }

    
}