<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Audit;



class AuditTransformer extends TransformerAbstract
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


    public function transform(Audit $audit)
    {
        $data= [ 
			"id" => $audit->id,
			"user_type" => $audit->user_type,
			"user_id" => $audit->user_id,
			"event" => $audit->event,
			"auditable_type" => $audit->auditable_type,
			"auditable_id" => $audit->auditable_id,
			"old_values" => $audit->old_values,
			"new_values" => $audit->new_values,
			"url" => $audit->url,
			"ip_address" => $audit->ip_address,
			"user_agent" => $audit->user_agent,
            "tags" => $audit->tags,
            "region_id" => $audit->region_id,
			"created_at" => $audit->created_at,
			"updated_at" => $audit->updated_at,

        ];
        return $this->filterFields($data);

    }

    
}