<?php
namespace App\Transformers\GroupTransformer;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Tblgroup;



class TblgroupTransformer extends TransformerAbstract
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


    public function transform(Tblgroup $tblgroup)
    {
        $data= [
			"id" => $tblgroup->id,
			"title" => $tblgroup->title,
			"region_id" => $tblgroup->region_id,
			"created_at" => $tblgroup->created_at,
			"updated_at" => $tblgroup->updated_at,
			"deleted_at" => $tblgroup->deleted_at,

        ];
        return $this->filterFields($data);

    }

    
}