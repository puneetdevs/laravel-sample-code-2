<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Client;



class ClientTransformer extends TransformerAbstract
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


    public function transform(Client $client)
    {
        $data= [
			"id" => $client->ID,
			"name" => $client->Name,
			"abbrevation" => $client->abbrevation,
            "address" => $client->AddressLine1,
            "AddressLine2" => $client->AddressLine2,
			"city" => $client->City,
			"providance" => $client->Prov,
			"postal_code" => $client->Postal,
			"country" => $client->Country,
			"email" => $client->email,
            "phone" => $client->ClientPhone,
            "PhoneExt" => $client->PhoneExt,
            "notes" => $client->ClientNotes,
            "fax" => $client->ClientFax,
            "status" => $client->status,
            "gst_number" => $client->GSTNumber,
			"created_at" => $client->created_at,
			"updated_at" => $client->updated_at,
		];
        return $this->filterFields($data);

    }

    
}