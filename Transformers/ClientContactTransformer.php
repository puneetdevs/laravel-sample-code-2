<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\ClientContact;



class ClientContactTransformer extends TransformerAbstract
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


    public function transform(ClientContact $clientContact)
    {
        $data= [
			"id" => $clientContact->ContactId,
			"client_id" => $clientContact->ClientId,
			"name" => $clientContact->ContactName,
			"title" => $clientContact->ContactTitle,
			"email" => $clientContact->Email,
			"home_phone" => $clientContact->Home,
			"work_phone" => $clientContact->Work,
            "cell_phone" => $clientContact->Cell,
            "ext" => $clientContact->Ext,
			"created_at" => $clientContact->created_at,
			"updated_at" => $clientContact->updated_at,
			"deleted_at" => $clientContact->deleted_at,

        ];
        return $this->filterFields($data);

    }

    
}