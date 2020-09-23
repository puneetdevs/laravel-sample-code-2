<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Tblworkcategory;



class TblworkcategoryTransformer extends TransformerAbstract
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


    public function transform(Tblworkcategory $work_categories)
    {
        $data= [
			"ID" => $work_categories->ID,
			"Description" => $work_categories->Description,
			"Code" => $work_categories->Code,
			"SortOrder" => $work_categories->SortOrder,
        ];
        return $this->filterFields($data);

    }

    
}