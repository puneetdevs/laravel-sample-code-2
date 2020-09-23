<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Position;



class PositionTransformer extends TransformerAbstract
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


    public function transform(Position $position)
    {
        $data= [
			"id" => $position->PID,
			"pos_id" => $position->pos_id,
            "name" => $position->Position,
            "is_supervisor" => $position->is_supervisor,
            "region_id" => $position->region_id,
            "status" => $position->status,
			"pos_order" => $position->SortOrder,
			"created_at" => $position->created_at,
			"updated_at" => $position->updated_at,

        ];
        return $this->filterFields($data);

    }

    
}