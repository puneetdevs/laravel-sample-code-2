<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\ChatGroup;



class ChatGroupTransformer extends TransformerAbstract
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


    public function transform(ChatGroup $chatGroup)
    {
        $data= [
			"id" => $chatGroup->id,
			"group_uuid" => $chatGroup->group_uuid,
			"event_id" => $chatGroup->event_id,
			"created_by" => $chatGroup->created_by,
			"group_name" => $chatGroup->group_name,
			"type" => $chatGroup->type,
			"created_at" => $chatGroup->created_at,
			"updated_at" => $chatGroup->updated_at,
			"deleted_at" => $chatGroup->deleted_at,

        ];
        return $this->filterFields($data);

    }

    
}